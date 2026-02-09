<?php

namespace App\Services;

use App\Models\EmailTemplate;

class EmailTemplateService
{
    public function render(
        string $key,
        array $data = [],
        ?string $defaultSubject = null,
        ?string $defaultHtml = null,
    ): array {
        $template = EmailTemplate::query()->where("key", $key)->first();

        if (!$template || !$template->is_active) {
            return [
                "subject" => $defaultSubject ?? "",
                "html" => $defaultHtml ?? "",
            ];
        }

        $subject = $this->interpolate($template->subject, $data);
        $html = $this->interpolate($template->body_html, $data);

        return [
            "subject" => $subject,
            "html" => $html,
        ];
    }

    private function interpolate(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $value = (string) $value;

            $content = str_replace("{{ {$key} }}", $value, $content);
            $content = str_replace("{{{$key}}}", $value, $content);
            $content = str_replace("{${key}}", $value, $content);
        }

        return $content;
    }
}
