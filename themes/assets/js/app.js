/**
 * App.js - utilitários globais de AJAX e UI
 * Requer jQuery (já incluído no painel).
 */

window.App = window.App || {};

(function (App, $) {
    "use strict";

    // ------------------------------------------------------
    // Configuração básica
    // ------------------------------------------------------

    App.config = App.config || {};

    // URL base (pode ser ajustada se necessário)
    App.config.baseUrl = App.config.baseUrl || window.location.origin;

    // Duração padrão de mensagens (ms)
    App.config.messageDuration = App.config.messageDuration || 5000;

    // ------------------------------------------------------
    // Utilitários de mensagens (alertas temporários / toasts)
    // ------------------------------------------------------

    const MESSAGE_TYPES = ["success", "error", "warning", "info"];

    function ensureMessageContainer() {
        let container = document.getElementById("app-message-container");
        if (!container) {
            container = document.createElement("div");
            container.id = "app-message-container";
            container.style.position = "fixed";
            container.style.top = "1rem";
            container.style.right = "1rem";
            container.style.zIndex = "9999";
            container.style.display = "flex";
            container.style.flexDirection = "column";
            container.style.gap = "0.5rem";
            document.body.appendChild(container);
        }
        return container;
    }

    function createMessageElement(text, type) {
        const el = document.createElement("div");
        el.className = `app-message app-message-${type}`;
        el.textContent = text;

        // Estilos mínimos (pode ser refinado no CSS do tema)
        el.style.minWidth = "260px";
        el.style.maxWidth = "380px";
        el.style.padding = "0.75rem 1rem";
        el.style.borderRadius = "4px";
        el.style.boxShadow = "0 2px 6px rgba(0,0,0,0.15)";
        el.style.backgroundColor = type === "success" ? "#198754" :
            type === "error" ? "#dc3545" :
                type === "warning" ? "#ffc107" : "#0d6efd";
        el.style.color = type === "warning" ? "#000" : "#fff";
        el.style.fontSize = "0.875rem";
        el.style.cursor = "pointer";

        el.addEventListener("click", function () {
            el.remove();
        });

        return el;
    }

    App.showMessage = function (text, type = "info", options = {}) {
        if (!text) return;

        if (!MESSAGE_TYPES.includes(type)) {
            type = "info";
        }

        const container = ensureMessageContainer();
        const el = createMessageElement(text, type);
        container.appendChild(el);

        const duration = options.duration || App.config.messageDuration;
        if (duration > 0) {
            setTimeout(function () {
                el.remove();
            }, duration);
        }
    };

    // Atalho de conveniência
    App.toast = App.showMessage;

    // ------------------------------------------------------
    // Modal genérico / confirmação operacional
    // ------------------------------------------------------

    let activeModalOptions = null;

    function ensureModal() {
        const modal = document.getElementById("appModal");
        if (!modal) {
            return null;
        }

        return {
            root: modal,
            title: modal.querySelector("#appModalTitle"),
            body: modal.querySelector("#appModalBody"),
            footer: modal.querySelector("#appModalFooter")
        };
    }

    function createModalButton(action) {
        const button = document.createElement("button");
        button.type = "button";
        button.className = action.className || "clinical-action-button";
        button.innerHTML = action.html || `<span>${action.label || "Continuar"}</span>`;

        button.addEventListener("click", function () {
            if (typeof action.onClick === "function") {
                action.onClick();
            }

            if (action.closeOnClick !== false) {
                App.closeModal();
            }
        });

        return button;
    }

    App.openModal = function (options = {}) {
        const modal = ensureModal();
        if (!modal) return;

        activeModalOptions = options;
        modal.title.textContent = options.title || "Janela operacional";

        if (options.html) {
            modal.body.innerHTML = options.html;
        } else {
            const message = options.message || options.text || "";
            modal.body.innerHTML = message ? `<p class="app-modal-copy">${message}</p>` : "";
        }

        modal.footer.innerHTML = "";
        const actions = Array.isArray(options.actions) && options.actions.length
            ? options.actions
            : [{ label: "Fechar", className: "clinical-action-button clinical-action-button-secondary" }];

        actions.forEach(function (action) {
            modal.footer.appendChild(createModalButton(action));
        });

        modal.root.hidden = false;
        modal.root.setAttribute("aria-hidden", "false");
        document.body.classList.add("has-app-modal");
    };

    App.closeModal = function () {
        const modal = ensureModal();
        if (!modal) return;

        modal.root.hidden = true;
        modal.root.setAttribute("aria-hidden", "true");
        document.body.classList.remove("has-app-modal");

        if (activeModalOptions && typeof activeModalOptions.onClose === "function") {
            activeModalOptions.onClose();
        }

        activeModalOptions = null;
    };

    App.confirm = function (options = {}) {
        App.openModal({
            title: options.title || "Confirmar operação",
            html: `
                <div class="app-modal-stack">
                    <p class="app-modal-copy">${options.message || "Confirme se pretende continuar com esta operação."}</p>
                    ${options.helpText ? `<p class="app-modal-help">${options.helpText}</p>` : ""}
                </div>
            `,
            actions: [
                {
                    label: options.cancelLabel || "Cancelar",
                    className: "clinical-action-button clinical-action-button-secondary"
                },
                {
                    label: options.confirmLabel || "Confirmar",
                    className: options.confirmClassName || "clinical-action-button clinical-action-button-danger",
                    onClick: function () {
                        if (typeof options.onConfirm === "function") {
                            options.onConfirm();
                        }
                    }
                }
            ]
        });
    };

    // ------------------------------------------------------
    // Wrapper AJAX genérico
    // ------------------------------------------------------

    App.request = function (options) {
        const defaults = {
            method: "POST",
            dataType: "json",
            cache: false,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        };

        const config = $.extend(true, {}, defaults, options || {});

        // Normaliza URL relativa com baseUrl se começar por "/" ou não tiver protocolo
        if (config.url && !/^https?:\/\//i.test(config.url)) {
            if (config.url.charAt(0) === "/") {
                config.url = App.config.baseUrl + config.url;
            }
        }

        const originalError = config.error;
        const originalSuccess = config.success;

        config.error = function (jqXHR, textStatus, errorThrown) {
            let msg = "Erro ao comunicar com o servidor. Tente novamente.";

            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                msg = jqXHR.responseJSON.message;
            }

            App.showMessage(msg, "error");

            if (typeof originalError === "function") {
                originalError(jqXHR, textStatus, errorThrown);
            }
        };

        config.success = function (response, textStatus, jqXHR) {
            // Convenção de resposta JSON do backend:
            // { success: bool, message: string, redirect?: string, errors?: object, html?: string }

            if (response && typeof response === "object") {
                if (response.message) {
                    App.showMessage(response.message, response.success ? "success" : "error");
                }

                if (response.redirect) {
                    setTimeout(function () {
                        window.location.href = response.redirect;
                    }, 800);
                }
            }

            if (typeof originalSuccess === "function") {
                originalSuccess(response, textStatus, jqXHR);
            }
        };

        return $.ajax(config);
    };

    // ------------------------------------------------------
    // Submissão de formulários via AJAX
    // ------------------------------------------------------
    // Uso: adicionar class="js-ajax-form" no <form> ou data-ajax="true".
    // O botão submit pode ter class="js-ajax-submit" para loading state.

    function setButtonLoading($btn, isLoading) {
        if (!$btn || !$btn.length) return;

        const loadingText = $btn.data("loading-text") || "Processando...";
        if (isLoading) {
            $btn.data("original-text", $btn.html());
            $btn.prop("disabled", true);
            $btn.addClass("is-loading");
            $btn.html(loadingText);
        } else {
            const originalText = $btn.data("original-text");
            if (originalText) {
                $btn.html(originalText);
            }
            $btn.prop("disabled", false);
            $btn.removeClass("is-loading");
        }
    }

    function formNeedsConfirmation($form) {
        return Boolean(
            $form.data("confirm") ||
            $form.data("confirmTitle") ||
            $form.data("confirmMessage")
        );
    }

    function confirmFormSubmission($form) {
        App.confirm({
            title: $form.data("confirmTitle") || "Confirmar operação",
            message: $form.data("confirmMessage") || "Confirme se pretende executar esta ação.",
            confirmLabel: $form.data("confirmLabel") || "Confirmar",
            onConfirm: function () {
                $form.data("skip-confirm", true);
                $form.trigger("submit");
            }
        });
    }

    function submitAjaxForm($form) {
        const action = $form.attr("action") || window.location.href;
        const method = ($form.attr("method") || "POST").toUpperCase();
        const $submitBtn = $form.find(".js-ajax-submit, [type='submit']").first();
        const formData = new FormData($form.get(0));

        setButtonLoading($submitBtn, true);

        App.request({
            url: action,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response && response.errors && typeof response.errors === "object") {
                    $form.find(".is-invalid").removeClass("is-invalid");

                    Object.keys(response.errors).forEach(function (field) {
                        const $field = $form.find("[name='" + field + "']");
                        if ($field.length) {
                            $field.addClass("is-invalid");
                        }
                    });
                }

                if (response && response.html && response.target) {
                    const $target = $(response.target);
                    if ($target.length) {
                        $target.html(response.html);
                    }
                }
            },
            complete: function () {
                setButtonLoading($submitBtn, false);
            }
        });
    }

    $(document).on("submit", "form.js-ajax-form, form[data-ajax='true']", function (e) {
        e.preventDefault();

        const $form = $(this);
        if ($form.data("skip-confirm")) {
            $form.removeData("skip-confirm");
            submitAjaxForm($form);
            return;
        }

        if (formNeedsConfirmation($form)) {
            confirmFormSubmission($form);
            return;
        }

        submitAjaxForm($form);
    });

    $(document).on("click", "[data-modal-close]", function () {
        App.closeModal();
    });

    $(document).on("click", "[data-modal-open]", function (e) {
        e.preventDefault();

        const $trigger = $(this);
        App.openModal({
            title: $trigger.data("modalTitle") || "Janela operacional",
            message: $trigger.data("modalMessage") || "",
            html: $trigger.data("modalHtml") || null
        });
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            App.closeModal();
        }
    });

    // ------------------------------------------------------
    // Helpers para SELECT / RADIO / CHECKBOX dinâmicos
    // ------------------------------------------------------

    /**
     * Preenche um <select> com dados vindos por AJAX.
     * @param selector string|HTMLElement|jQuery
     * @param url string
     * @param options object
     *   - params: dados adicionais a enviar
     *   - method: GET/POST (padrão: GET)
     *   - valueField: campo do objeto para o value (padrão: 'id')
     *   - textField: campo do objeto para o texto (padrão: 'text' ou 'name')
     *   - placeholder: texto opcional da primeira opção vazia
     *   - selected: valor a pré-selecionar
     */
    App.populateSelect = function (selector, url, options) {
        const $select = $(selector);
        if (!$select.length) return;

        const opts = $.extend({
            params: {},
            method: "GET",
            valueField: "id",
            textField: "text",
            placeholder: null,
            selected: null
        }, options || {});

        // Limpa e mostra estado de carregamento
        $select.prop("disabled", true).empty();
        if (opts.placeholder) {
            $select.append($('<option>', { value: "", text: opts.placeholder }));
        }

        App.request({
            url: url,
            method: opts.method,
            data: opts.params,
            success: function (response) {
                if (!response) return;

                let list = [];
                if (Array.isArray(response)) {
                    list = response;
                } else if (Array.isArray(response.data)) {
                    list = response.data;
                } else if (Array.isArray(response.items)) {
                    list = response.items;
                }

                list.forEach(function (item) {
                    const value = item[opts.valueField];
                    const text = item[opts.textField] || item.name || item.label || value;
                    const option = $('<option>', { value: value, text: text });

                    if (opts.selected !== null && String(opts.selected) === String(value)) {
                        option.prop("selected", true);
                    }

                    $select.append(option);
                });

                $select.prop("disabled", false);
            },
            error: function () {
                $select.prop("disabled", false);
            }
        });
    };

    /**
     * Preenche uma lista de checkboxes dinamicamente dentro de um container.
     * Espera que o backend devolva um array de objetos.
     * @param containerSelector string|HTMLElement|jQuery
     * @param url string
     * @param options object
     *   - name: atributo name para os checkboxes
     *   - params: dados adicionais a enviar
     *   - valueField: campo do objeto para o value (padrão: 'id')
     *   - textField: campo do objeto para o texto (padrão: 'text' ou 'name')
     *   - selectedValues: array de valores já selecionados
     */
    App.populateCheckboxList = function (containerSelector, url, options) {
        const $container = $(containerSelector);
        if (!$container.length) return;

        const opts = $.extend({
            name: "items[]",
            params: {},
            valueField: "id",
            textField: "text",
            selectedValues: []
        }, options || {});

        const selectedSet = new Set((opts.selectedValues || []).map(String));

        $container.empty();

        App.request({
            url: url,
            method: "GET",
            data: opts.params,
            success: function (response) {
                let list = [];
                if (Array.isArray(response)) {
                    list = response;
                } else if (Array.isArray(response.data)) {
                    list = response.data;
                } else if (Array.isArray(response.items)) {
                    list = response.items;
                }

                list.forEach(function (item) {
                    const value = item[opts.valueField];
                    const text = item[opts.textField] || item.name || item.label || value;
                    const id = `${opts.name.replace(/\W+/g, '_')}_${value}`;

                    const $wrapper = $('<label>', { class: 'checkbox-inline d-block mb-1' });
                    const $checkbox = $('<input>', {
                        type: 'checkbox',
                        name: opts.name,
                        value: value,
                        id: id
                    });

                    if (selectedSet.has(String(value))) {
                        $checkbox.prop('checked', true);
                    }

                    const $span = $('<span>').text(" " + text);

                    $wrapper.append($checkbox).append($span);
                    $container.append($wrapper);
                });
            }
        });
    };

    /**
     * Helper para listas de radios dinâmicas.
     * @param containerSelector
     * @param url
     * @param options (name, params, valueField, textField, selected)
     */
    App.populateRadioList = function (containerSelector, url, options) {
        const $container = $(containerSelector);
        if (!$container.length) return;

        const opts = $.extend({
            name: "option",
            params: {},
            valueField: "id",
            textField: "text",
            selected: null
        }, options || {});

        $container.empty();

        App.request({
            url: url,
            method: "GET",
            data: opts.params,
            success: function (response) {
                let list = [];
                if (Array.isArray(response)) {
                    list = response;
                } else if (Array.isArray(response.data)) {
                    list = response.data;
                } else if (Array.isArray(response.items)) {
                    list = response.items;
                }

                list.forEach(function (item) {
                    const value = item[opts.valueField];
                    const text = item[opts.textField] || item.name || item.label || value;
                    const id = `${opts.name.replace(/\W+/g, '_')}_${value}`;

                    const $wrapper = $('<label>', { class: 'radio-inline d-block mb-1' });
                    const $radio = $('<input>', {
                        type: 'radio',
                        name: opts.name,
                        value: value,
                        id: id
                    });

                    if (opts.selected !== null && String(opts.selected) === String(value)) {
                        $radio.prop('checked', true);
                    }

                    const $span = $('<span>').text(" " + text);

                    $wrapper.append($radio).append($span);
                    $container.append($wrapper);
                });
            }
        });
    };

    // ------------------------------------------------------
    // Helper para selects dependentes (cascata)
    // ------------------------------------------------------
    // Exemplo de uso:
    // App.bindDependentSelect('#estado', '#cidade', '/api/cidades', 'estado_id');

    App.bindDependentSelect = function (parentSelector, childSelector, url, parentParamName) {
        const $parent = $(parentSelector);
        const $child = $(childSelector);

        if (!$parent.length || !$child.length) return;

        $parent.on('change', function () {
            const value = $(this).val();
            if (!value) {
                $child.empty().prop('disabled', true);
                return;
            }

            App.populateSelect($child, url, {
                params: { [parentParamName]: value },
                method: 'GET',
                placeholder: 'Selecione...'
            });
        });
    };

})(window.App, window.jQuery);
