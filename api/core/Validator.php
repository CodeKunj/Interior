<?php
/**
 * Declarative Input Validation Engine
 */

declare(strict_types=1);

namespace Atelier\Core;

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Factory shortcut
     */
    public static function make(array $data, array $rules): self {
        $validator = new self($data, $rules);
        $validator->validate();
        return $validator;
    }

    /**
     * Run validation rules against data
     */
    public function validate(): bool {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $rulesList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $this->data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $this->applyRule($field, $value, $rule, $params);
            }
        }

        return empty($this->errors);
    }

    /**
     * Check single rule
     */
    private function applyRule(string $field, mixed $value, string $rule, array $params): void {
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, "The {$field} field is required.");
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Please enter a valid email address.");
                }
                break;

            case 'min':
                $min = intval($params[0] ?? 0);
                if (is_string($value) && mb_strlen(trim($value)) < $min) {
                    $this->addError($field, "The {$field} must be at least {$min} characters.");
                } elseif (is_numeric($value) && floatval($value) < $min) {
                    $this->addError($field, "The {$field} must be at least {$min}.");
                }
                break;

            case 'max':
                $max = intval($params[0] ?? 255);
                if (is_string($value) && mb_strlen(trim($value)) > $max) {
                    $this->addError($field, "The {$field} may not exceed {$max} characters.");
                } elseif (is_numeric($value) && floatval($value) > $max) {
                    $this->addError($field, "The {$field} may not exceed {$max}.");
                }
                break;

            case 'in':
                if ($value !== null && $value !== '' && !in_array((string)$value, $params, true)) {
                    $allowed = implode(', ', $params);
                    $this->addError($field, "The selected {$field} must be one of: {$allowed}.");
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, "The {$field} must be a valid number.");
                }
                break;

            case 'url':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, "The {$field} must be a valid URL.");
                }
                break;
        }
    }

    /**
     * Add error message for field
     */
    private function addError(string $field, string $message): void {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool {
        return !empty($this->errors);
    }

    /**
     * Get associative array of errors
     */
    public function errors(): array {
        return $this->errors;
    }
}
