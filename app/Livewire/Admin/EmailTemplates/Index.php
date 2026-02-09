<?php

namespace App\Livewire\Admin\EmailTemplates;

use App\Models\EmailTemplate;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = "";
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public bool $showModal = false;
    public ?int $editingId = null;

    public array $form = [
        "key" => "",
        "name" => "",
        "subject" => "",
        "body_html" => "",
        "variables" => "",
        "is_active" => true,
    ];

    protected $queryString = [
        "search" => ["except" => ""],
        "sortField" => ["except" => "created_at"],
        "sortDirection" => ["except" => "desc"],
        "page" => ["except" => 1],
    ];

    public function updated($name): void
    {
        if (in_array($name, ["search", "sortField", "sortDirection"], true)) {
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        return [
            "form.key" => [
                "required",
                "string",
                "max:120",
                Rule::unique("email_templates", "key")->ignore($this->editingId),
            ],
            "form.name" => ["required", "string", "max:160"],
            "form.subject" => ["required", "string", "max:200"],
            "form.body_html" => ["required", "string"],
            "form.variables" => ["nullable", "string"],
            "form.is_active" => ["boolean"],
        ];
    }

    public function openCreate(): void
    {
        $this->resetErrorBag();
        $this->editingId = null;
        $this->form = [
            "key" => "",
            "name" => "",
            "subject" => "",
            "body_html" => "",
            "variables" => "",
            "is_active" => true,
        ];
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $template = EmailTemplate::findOrFail($id);

        $this->form = [
            "key" => $template->key,
            "name" => $template->name,
            "subject" => $template->subject,
            "body_html" => $template->body_html,
            "variables" => is_array($template->variables) ? implode(", ", $template->variables) : "",
            "is_active" => (bool) $template->is_active,
        ];

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $variables = [];
        if (!empty($this->form["variables"])) {
            $variables = collect(explode(",", $this->form["variables"]))
                ->map(fn($v) => trim($v))
                ->filter()
                ->values()
                ->all();
        }

        $payload = [
            "key" => $this->form["key"],
            "name" => $this->form["name"],
            "subject" => $this->form["subject"],
            "body_html" => $this->form["body_html"],
            "variables" => $variables,
            "is_active" => (bool) $this->form["is_active"],
        ];

        if ($this->editingId) {
            EmailTemplate::findOrFail($this->editingId)->update($payload);
        } else {
            EmailTemplate::create($payload);
        }

        $this->showModal = false;
        $this->dispatch("notify", message: "Template salvo.");
    }

    public function toggleStatus(int $id): void
    {
        $template = EmailTemplate::findOrFail($id);
        $template->update(["is_active" => !$template->is_active]);
    }

    public function render()
    {
        $allowedSorts = ["name", "key", "created_at"]; 
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        $templates = EmailTemplate::query()
            ->when($this->search !== "", function ($q) {
                $search = $this->search;
                $q->where(function ($qq) use ($search) {
                    $qq->where("name", "like", "%{$search}%")
                        ->orWhere("key", "like", "%{$search}%")
                        ->orWhere("subject", "like", "%{$search}%");
                });
            })
            ->orderBy($sortField, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $suggestedVariables = [
            "user_name",
            "user_email",
            "reset_url",
            "renew_url",
            "days_left",
            "plan_name",
        ];

        return view("livewire.admin.email-templates.index", [
            "templates" => $templates,
            "suggestedVariables" => $suggestedVariables,
        ])->layout("layouts.admin.app");
    }
}
