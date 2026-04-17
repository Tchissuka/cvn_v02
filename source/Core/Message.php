<?php

namespace Source\Core;

/**
 * Class Message
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Core
 */
class Message
{
    private $text;
    private $type;

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
        return $this->text;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function info(string $message): Message
    {
        $this->type = CONF_MESSAGE_INFO;
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function success(string $message): Message
    {
        $this->type = CONF_MESSAGE_SUCCESS;
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function warning(string $message): Message
    {
        $this->type = CONF_MESSAGE_WARNING;
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function error(string $message): Message
    {
        $this->type = CONF_MESSAGE_ERROR;
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $type = $this->normalizedType();
        $icon = $this->iconClass($type);

        return "<div class='" . CONF_MESSAGE_CLASS . " {$this->getType()} app-alert app-alert-{$type}' role='alert' aria-live='polite'><span class='app-alert-icon' aria-hidden='true'><i class='fas {$icon}'></i></span><div class='app-alert-content'>{$this->getText()}</div></div>";
    }

    /**
     * @return string
     */
    public function json(): string
    {
        return json_encode(["error" => $this->getText()]);
    }

    /**
     * Set flash Session Key
     */
    public function flash(): void
    {
        (new Session())->set("flash", $this);
    }

    /**
     * @param string $message
     * @return string
     */
    private function filter(string $message): string
    {
        return filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
