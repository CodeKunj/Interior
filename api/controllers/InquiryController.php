<?php
/**
 * Inquiry Controller
 * Handles client consultation requests, data validation, and email dispatch.
 */

declare(strict_types=1);

namespace Atelier\Controllers;

use Atelier\Core\Request;
use Atelier\Core\Response;
use Atelier\Core\Validator;
use Atelier\Core\Mailer;
use Atelier\Models\Inquiry;

class InquiryController {
    private Inquiry $inquiryModel;
    private Mailer $mailer;

    public function __construct() {
        $this->inquiryModel = new Inquiry();
        $this->mailer = new Mailer();
    }

    /**
     * POST /api/inquiry.php
     * Record new client inquiry and trigger notification email
     */
    public function submit(Request $request): void {
        $request->requireMethod('POST');

        // Extract and normalize payload supporting both camelCase and snake_case
        $name         = $request->getClean('name') ?: $request->getClean('fullName');
        $email        = $request->getClean('email');
        $phone        = $request->getClean('phone');
        $city         = $request->getClean('city');
        $propertyType = $request->getClean('property_type') ?: $request->getClean('propertyType', 'Residential');
        $areaSqft     = $request->getClean('area_sqft') ?: $request->getClean('areaSqft', 'Not Specified');
        $budgetRange  = $request->getClean('budget_range') ?: $request->getClean('budgetRange', 'Flexible');
        $message      = $request->getClean('message');

        $data = [
            'name'          => $name,
            'email'         => $email,
            'phone'         => $phone,
            'city'          => $city,
            'property_type' => $propertyType,
            'area_sqft'     => $areaSqft,
            'budget_range'  => $budgetRange,
            'message'       => $message
        ];

        // Validate payload
        $validator = Validator::make($data, [
            'name'    => 'required|min:2|max:150',
            'email'   => 'required|email|max:150',
            'phone'   => 'required|min:5|max:50',
            'message' => 'required|min:5|max:5000'
        ]);

        if ($validator->fails()) {
            Response::validationFailed($validator->errors(), 'Please correct the highlighted fields in your dossier.');
        }

        // Insert into database
        $inquiryId = $this->inquiryModel->create($data);
        $data['id'] = $inquiryId;

        // Dispatch email notification to studio concierge
        $this->mailer->sendInquiryNotification($data);

        Response::success([
            'inquiry_id' => $inquiryId,
            'client'     => $name,
            'received'   => true
        ], "Your consultation dossier #{$inquiryId} has been successfully transmitted. Our design director will review your project parameters within 24 business hours.", 201);
    }
}
