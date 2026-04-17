<?php

namespace Source\Support;

use Source\Core\Session;

/**
 * FSPHP | Class Message
 *
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Core
 */
class Message
{
    /** @var string */
    private $text;
    private $textBtn;
    private $typeBtn;
    private $icon;

    /** @var string */
    private $type;
    private $before;
    private $after;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->before . $this->text . $this->after;
    }

    /**
     * Mandar texto do botão *
     * @return string
     */
    public function getTextBtn(): ?string
    {
        return $this->icon . ' ' . $this->textBtn;
    }
    /**
     * Btn type
     *
     * @return string
     */
    public function getTypeBtn(): ?string
    {
        return $this->typeBtn;
    }
    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }
    /**
     * before
     *
     * @param  mixed $text
     * @return Message
     */
    public function before(string $text): Message
    {
        $this->before = $text;
        return $this;
    }

    public function after(string $text): Message
    {
        $this->after = $text;
        return $this;
    }
    /**
     * @param string $message
     * @return Message
     */
    public function info(string $message): Message
    {
        $this->type = "info";
        $this->text = $this->filter($message);
        return $this;
    }
    public function unset(): void
    {
        unset($this->type, $this->text);
    }
    /**
     * @param string $message
     * @return Message
     */
    public function success(string $message): Message
    {
        $this->type = "success";
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function warning(string $message): Message
    {
        $this->type = "warning";
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function error(string $message): Message
    {
        $this->type = "error";
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @return string     * 
     */
    public function render(): string
    {
        $type = $this->normalizedType();
        $icon = $this->iconClass($type);

        return "<div class='alert {$this->getType()} app-alert app-alert-{$type}' role='alert' aria-live='polite'><span class='app-alert-icon' aria-hidden='true'><i class='fas {$icon}'></i></span><div class='app-alert-content'>{$this->getText()}</div></div>";
    }

    /**
     * @return string
     */
    public function json(): void
    {
        echo json_encode(["message" => ["type" => $this->getType(), "text" => $this->getText()]]);
    }

    /**
     * mostra ao enviar o formulario
     * @return array
     */
    public function MessJsonIn(): array
    {
        return [
            "type" => $this->getType(),
            "text" => $this->getText()
        ];
    }

    /**
     * Set flash Session Key
     */
    public function flash(): void
    {
        $this->type = 'alert-' . ($this->getType() == 'error' ? 'danger' : $this->getType());
        (new Session())->set("flash", $this);
    }

    /**
     * Set flash json key
     */
    public function flashJson(): void
    {
        (new Session())->set("flashJson", ["message" => ["text" => $this->text, "type" => $this->type]]);
    }

    /**
     * @param string $message
     * @return string
     */
    private function filter(string $message): string
    {
        return filter_var($message, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    private function normalizedType(): string
    {
        $type = strtolower((string)$this->getType());

        return match (true) {
            str_contains($type, 'danger'), str_contains($type, 'error') => 'error',
            str_contains($type, 'warning') => 'warning',
            str_contains($type, 'success') => 'success',
            default => 'info',
        };
    }

    private function iconClass(string $type): string
    {
        return match ($type) {
            'error' => 'fa-circle-exclamation',
            'warning' => 'fa-triangle-exclamation',
            'success' => 'fa-circle-check',
            default => 'fa-circle-info',
        };
    }
}
