<?php
/**
 * Atelier Vérité CMS — Client Consultation Inquiries & Leads Manager
 * Filter by status, view detailed design dossiers, update statuses, and export to CSV.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

use Atelier\Models\Inquiry;

require_admin_auth();

$inquiryModel = null;
$dbError = null;

try {
    $inquiryModel = new Inquiry();
} catch (\Throwable $e) {
    $dbError = $e->getMessage();
}

$feedback = $dbError ? "Database Setup Required: {$dbError}. Import api/schema.sql and configure .env." : '';
$feedbackType = $dbError ? 'error' : 'success';

// -------------------------------------------------------------
// CSV EXPORT HANDLER (?export=csv)
// -------------------------------------------------------------
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $statusFilter = trim($_GET['status'] ?? '');
    $leads = $inquiryModel->getAll($statusFilter, 5000);

    $filename = 'atelier_consultation_leads_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // Output UTF-8 BOM for Microsoft Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // CSV Header row
    fputcsv($output, [
        'ID',
        'Submission Date',
        'Client Name',
        'Email Address',
        'Phone Number',
        'Property Typology',
        'Carpet Area (sq. ft.)',
        'Estimated Budget',
        'City / Location',
        'Status',
        'Client Vision & Message'
    ]);

    foreach ($leads as $lead) {
        fputcsv($output, [
            $lead['id'],
            $lead['created_at'],
            $lead['name'],
            $lead['email'],
            $lead['phone'],
            $lead['property_type'],
            $lead['area_sqft'],
            $lead['budget_range'],
            $lead['city'],
            strtoupper($lead['status']),
            $lead['message']
        ]);
    }

    fclose($output);
    exit();
}

// -------------------------------------------------------------
// STATUS UPDATE HANDLER
// -------------------------------------------------------------
if (isset($_GET['set_status']) && isset($_GET['inquiry_id'])) {
    $inqId = intval($_GET['inquiry_id']);
    $newStatus = trim($_GET['set_status']);
    if ($inqId > 0 && in_array($newStatus, ['new', 'contacted', 'scheduled', 'archived'], true)) {
        try {
            $inquiryModel->updateStatus($inqId, $newStatus);
            header("Location: leads.php?status=" . urlencode($_GET['status'] ?? '') . "&msg=status_updated");
            exit();
        } catch (\Throwable $e) {
            $feedback = 'Failed to update status: ' . $e->getMessage();
            $feedbackType = 'error';
        }
    }
}

// -------------------------------------------------------------
// DELETE HANDLER
// -------------------------------------------------------------
if (isset($_GET['delete_id'])) {
    $delId = intval($_GET['delete_id']);
    if ($delId > 0) {
        try {
            $inquiryModel->delete($delId);
            header("Location: leads.php?msg=deleted");
            exit();
        } catch (\Throwable $e) {
            $feedback = 'Error deleting inquiry: ' . $e->getMessage();
            $feedbackType = 'error';
        }
    }
}

// Flash messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'status_updated') $feedback = 'Client inquiry status successfully updated.';
    if ($_GET['msg'] === 'deleted') $feedback = 'Client inquiry dossier deleted.';
}

// Data fetching
$activeStatus = trim($_GET['status'] ?? '');
$searchQuery = trim($_GET['search'] ?? '');
$viewId = intval($_GET['view'] ?? 0);

$counts = ['total' => 0, 'new' => 0, 'contacted' => 0, 'scheduled' => 0, 'archived' => 0];
$inquiries = [];

if ($inquiryModel) {
    try {
        $counts = $inquiryModel->getCountsByStatus();
        $inquiries = $inquiryModel->getAll($activeStatus, 200);
    } catch (\Throwable $e) {}
}

// Client search filtering
if ($searchQuery !== '' && !empty($inquiries)) {
    $q = mb_strtolower($searchQuery);
    $inquiries = array_filter($inquiries, function ($item) use ($q) {
        return (strpos(mb_strtolower($item['name']), $q) !== false) ||
               (strpos(mb_strtolower($item['email']), $q) !== false) ||
               (strpos(mb_strtolower($item['phone']), $q) !== false) ||
               (strpos(mb_strtolower($item['city']), $q) !== false) ||
               (strpos(mb_strtolower($item['property_type']), $q) !== false);
    });
}

// Detailed view dossier
$viewItem = null;
if ($viewId > 0 && $inquiryModel) {
    try {
        $viewItem = $inquiryModel->findById($viewId);
    } catch (\Throwable $e) {}
}

render_admin_layout_start(
    'Consultation Leads & Inquiries',
    'leads',
    '',
    [['label' => 'Consultation Leads', 'url' => '']]
);
?>

<div class="space-y-8">

    <!-- Flash message -->
    <?php if ($feedback): ?>
        <div class="<?= $feedbackType === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800' ?> border p-4 rounded-2xl text-xs flex items-center justify-between shadow-sm">
            <span><?= htmlspecialchars($feedback) ?></span>
            <button onclick="this.parentElement.remove()" class="text-stone hover:text-charcoal font-bold text-base px-2">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Header & Export Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="text-[10px] uppercase tracking-[0.25em] text-accent font-semibold block mb-1">Private Commissions</span>
            <h1 class="font-serif text-3xl sm:text-4xl text-charcoal">Consultation Inquiries & Leads</h1>
            <p class="text-stone text-xs font-light mt-1">Review prospective client dossiers, property specifications, and schedule consultations.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="leads.php?export=csv<?= $activeStatus ? '&status=' . urlencode($activeStatus) : '' ?>" class="inline-flex items-center space-x-2 bg-charcoal text-white hover:bg-accent px-6 py-3 rounded-full text-xs uppercase tracking-[0.16em] font-semibold transition-all shadow-md">
                <span>&darr; Export CSV (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Status Filters & Search Bar -->
    <div class="bg-white border border-sand rounded-2xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex items-center flex-wrap gap-2 text-xs">
            <a href="leads.php" class="px-3.5 py-1.5 rounded-full text-[11px] uppercase tracking-wider transition-colors <?= empty($activeStatus) ? 'bg-charcoal text-white font-medium shadow-sm' : 'bg-linen hover:bg-sand text-stone hover:text-charcoal' ?>">
                All (<?= $counts['total'] ?>)
            </a>
            <a href="leads.php?status=new" class="px-3.5 py-1.5 rounded-full text-[11px] uppercase tracking-wider transition-colors <?= $activeStatus === 'new' ? 'bg-amber-100 text-amber-900 border border-amber-300 font-semibold shadow-sm' : 'bg-linen hover:bg-sand text-stone hover:text-charcoal' ?>">
                New (<?= $counts['new'] ?>)
            </a>
            <a href="leads.php?status=contacted" class="px-3.5 py-1.5 rounded-full text-[11px] uppercase tracking-wider transition-colors <?= $activeStatus === 'contacted' ? 'bg-blue-100 text-blue-900 border border-blue-300 font-semibold shadow-sm' : 'bg-linen hover:bg-sand text-stone hover:text-charcoal' ?>">
                Contacted (<?= $counts['contacted'] ?>)
            </a>
            <a href="leads.php?status=scheduled" class="px-3.5 py-1.5 rounded-full text-[11px] uppercase tracking-wider transition-colors <?= $activeStatus === 'scheduled' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-semibold shadow-sm' : 'bg-linen hover:bg-sand text-stone hover:text-charcoal' ?>">
                Scheduled (<?= $counts['scheduled'] ?>)
            </a>
            <a href="leads.php?status=archived" class="px-3.5 py-1.5 rounded-full text-[11px] uppercase tracking-wider transition-colors <?= $activeStatus === 'archived' ? 'bg-stone/20 text-charcoal font-semibold shadow-sm' : 'bg-linen hover:bg-sand text-stone hover:text-charcoal' ?>">
                Archived (<?= $counts['archived'] ?>)
            </a>
        </div>

        <!-- Search input -->
        <form method="GET" class="w-full md:w-72">
            <?php if ($activeStatus): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($activeStatus) ?>">
            <?php endif; ?>
            <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search by name, city, email..." class="w-full bg-marble border border-sand rounded-xl px-4 py-2 text-xs text-charcoal focus:outline-none focus:border-accent">
        </form>
    </div>

    <!-- DETAIL MODAL / FLYOUT IF ?view=id -->
    <?php if ($viewItem): ?>
        <div class="bg-white border-2 border-accent/40 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6 relative">
            <div class="flex items-start justify-between pb-4 border-b border-sand">
                <div>
                    <span class="text-[10px] uppercase tracking-[0.25em] text-accent font-semibold block mb-1">
                        Dossier #<?= $viewItem['id'] ?> &bull; Received <?= date('M d, Y \a\t h:i A', strtotime($viewItem['created_at'])) ?>
                    </span>
                    <h2 class="font-serif text-3xl text-charcoal"><?= htmlspecialchars($viewItem['name']) ?></h2>
                    <span class="text-xs text-stone font-light"><?= htmlspecialchars($viewItem['city'] ?: 'City Not Specified') ?></span>
                </div>
                <a href="leads.php<?= $activeStatus ? '?status=' . urlencode($activeStatus) : '' ?>" class="text-stone hover:text-charcoal text-xl font-bold px-3 py-1 bg-marble rounded-full border border-sand">
                    &times;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 p-5 bg-linen/50 rounded-2xl border border-sand">
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-stone block font-medium">Direct Email</span>
                    <a href="mailto:<?= htmlspecialchars($viewItem['email']) ?>" class="text-xs font-semibold text-accent hover:underline break-all">
                        <?= htmlspecialchars($viewItem['email']) ?>
                    </a>
                </div>
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-stone block font-medium">Phone & WhatsApp</span>
                    <div class="text-xs font-semibold text-charcoal">
                        <?= htmlspecialchars($viewItem['phone']) ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $viewItem['phone']) ?>" target="_blank" class="ml-1 text-emerald-600 hover:underline text-[11px]">
                            [WhatsApp &nearr;]
                        </a>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-stone block font-medium">Property Typology</span>
                    <span class="text-xs font-semibold text-charcoal"><?= htmlspecialchars($viewItem['property_type']) ?></span>
                </div>
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-stone block font-medium">Carpet Area & Budget</span>
                    <span class="text-xs font-semibold text-charcoal">
                        <?= htmlspecialchars($viewItem['area_sqft']) ?> &bull; <?= htmlspecialchars($viewItem['budget_range']) ?>
                    </span>
                </div>
            </div>

            <div>
                <strong class="text-xs uppercase tracking-wider text-stone block mb-2 font-medium">Client Vision & Space Requirements</strong>
                <div class="bg-marble border border-sand rounded-2xl p-5 text-xs text-charcoal leading-relaxed whitespace-pre-wrap">
                    <?= htmlspecialchars($viewItem['message']) ?>
                </div>
            </div>

            <!-- Status updater bar inside modal -->
            <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-sand">
                <div class="flex items-center space-x-2 text-xs">
                    <span class="text-stone uppercase tracking-wider text-[11px] mr-2">Set Status:</span>
                    <a href="leads.php?inquiry_id=<?= $viewItem['id'] ?>&set_status=new" class="px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-semibold border <?= $viewItem['status'] === 'new' ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-white hover:bg-sand text-stone' ?>">New</a>
                    <a href="leads.php?inquiry_id=<?= $viewItem['id'] ?>&set_status=contacted" class="px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-semibold border <?= $viewItem['status'] === 'contacted' ? 'bg-blue-100 text-blue-900 border-blue-300' : 'bg-white hover:bg-sand text-stone' ?>">Contacted</a>
                    <a href="leads.php?inquiry_id=<?= $viewItem['id'] ?>&set_status=scheduled" class="px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-semibold border <?= $viewItem['status'] === 'scheduled' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-white hover:bg-sand text-stone' ?>">Scheduled</a>
                    <a href="leads.php?inquiry_id=<?= $viewItem['id'] ?>&set_status=archived" class="px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-semibold border <?= $viewItem['status'] === 'archived' ? 'bg-stone/20 text-charcoal' : 'bg-white hover:bg-sand text-stone' ?>">Archive</a>
                </div>

                <div>
                    <a href="leads.php?delete_id=<?= $viewItem['id'] ?>" onclick="return confirm('Permanently delete this inquiry?')" class="text-red-500 hover:text-red-700 text-xs uppercase tracking-wider font-semibold">
                        Delete Dossier
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Inquiries Table -->
    <div class="bg-white border border-sand rounded-3xl overflow-hidden shadow-[0_4px_25px_rgba(28,27,25,0.03)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-linen text-stone text-[10px] uppercase tracking-[0.18em] border-b border-sand">
                    <tr>
                        <th class="p-4 pl-6">Date</th>
                        <th class="p-4">Client Name & City</th>
                        <th class="p-4">Contact Details</th>
                        <th class="p-4">Property & Budget</th>
                        <th class="p-4">Message Snippet</th>
                        <th class="p-4">Status & Update</th>
                        <th class="p-4 pr-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand/60">
                    <?php if (empty($inquiries)): ?>
                        <tr>
                            <td colspan="7" class="p-12 text-center text-stone italic text-xs">
                                No consultation requests found matching your criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr class="hover:bg-marble/60 transition-colors">
                                <td class="p-4 pl-6 text-[11px] text-stone whitespace-nowrap">
                                    <?= date('M d, Y', strtotime($inq['created_at'])) ?><br>
                                    <span class="text-[10px] text-stone/70"><?= date('h:i A', strtotime($inq['created_at'])) ?></span>
                                </td>
                                <td class="p-4 font-medium text-charcoal">
                                    <div class="font-serif text-sm text-charcoal"><?= htmlspecialchars($inq['name']) ?></div>
                                    <div class="text-[11px] text-stone font-normal"><?= htmlspecialchars($inq['city'] ?: 'City Not Specified') ?></div>
                                </td>
                                <td class="p-4 text-[11px] text-stone whitespace-nowrap">
                                    <a href="mailto:<?= htmlspecialchars($inq['email']) ?>" class="text-accent hover:underline block font-medium"><?= htmlspecialchars($inq['email']) ?></a>
                                    <a href="tel:<?= htmlspecialchars($inq['phone']) ?>" class="text-stone hover:text-charcoal"><?= htmlspecialchars($inq['phone']) ?></a>
                                </td>
                                <td class="p-4 text-[11px] text-stone">
                                    <strong class="text-charcoal font-medium"><?= htmlspecialchars($inq['property_type']) ?></strong><br>
                                    <span><?= htmlspecialchars($inq['area_sqft']) ?> &bull; <?= htmlspecialchars($inq['budget_range']) ?></span>
                                </td>
                                <td class="p-4 text-[11px] text-stone max-w-xs truncate">
                                    <?= htmlspecialchars($inq['message']) ?>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] uppercase tracking-wider font-semibold 
                                        <?= $inq['status'] === 'new' ? 'bg-amber-100 text-amber-900 border border-amber-300' : '' ?>
                                        <?= $inq['status'] === 'contacted' ? 'bg-blue-100 text-blue-900 border border-blue-300' : '' ?>
                                        <?= $inq['status'] === 'scheduled' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : '' ?>
                                        <?= $inq['status'] === 'archived' ? 'bg-stone/10 text-stone border border-sand' : '' ?>
                                    ">
                                        <?= strtoupper(htmlspecialchars($inq['status'])) ?>
                                    </span>
                                    <div class="text-[10px] text-stone mt-1.5 space-x-1">
                                        <a href="leads.php?inquiry_id=<?= $inq['id'] ?>&set_status=contacted" class="hover:text-charcoal underline">Contacted</a>
                                        <span>&bull;</span>
                                        <a href="leads.php?inquiry_id=<?= $inq['id'] ?>&set_status=scheduled" class="hover:text-charcoal underline">Scheduled</a>
                                        <span>&bull;</span>
                                        <a href="leads.php?inquiry_id=<?= $inq['id'] ?>&set_status=archived" class="hover:text-stone/60 underline">Archive</a>
                                    </div>
                                </td>
                                <td class="p-4 pr-6 text-right whitespace-nowrap space-x-2">
                                    <a href="leads.php?view=<?= $inq['id'] ?><?= $activeStatus ? '&status=' . urlencode($activeStatus) : '' ?>" class="text-accent hover:text-charcoal text-[11px] uppercase font-semibold">
                                        Dossier &rarr;
                                    </a>
                                    <a href="leads.php?delete_id=<?= $inq['id'] ?>" onclick="return confirm('Permanently remove this inquiry?')" class="text-red-500 hover:text-red-700 text-[11px] uppercase font-medium">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php render_admin_layout_end(); ?>
