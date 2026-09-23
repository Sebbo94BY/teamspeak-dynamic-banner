<script type="module">
    $(document).ready(function () {
        const $form = $('#template-configuration');
        let previewVariables = {{ Illuminate\Support\Js::from($preview_variables) }};
        const previewVariablesUrl = $('.banner-preview-sticky').data('preview-variables-url');
        const previews = [];
        const loadedFonts = new Set();
        let activeRow = null;

        $('.live-preview').each(function () {
            const canvas = this;
            const background = new Image();
            canvas.width = Number(canvas.dataset.width);
            canvas.height = Number(canvas.dataset.height);
            canvas.style.cursor = 'crosshair';
            previews.push({ canvas, context: canvas.getContext('2d'), background });
            background.onload = redrawPreview;
            background.src = canvas.dataset.imageSrc;
        });

        function visibleRows() {
            return $('.banner-config-row').filter(function () {
                return !$(this).hasClass('d-none');
            });
        }

        function inputInRow($row, name) {
            return $row.find('[name="configuration[' + name + '][]"]');
        }

        function selectActiveRow($row) {
            if (!$row || !$row.length) return;
            activeRow = $row;
            $('.banner-config-row').removeClass('border border-primary rounded p-2');
            activeRow.addClass('border border-primary rounded p-2');
        }

        function loadFont(option) {
            const family = option.data('font-family');
            const url = option.data('font-url');
            if (!family || !url || loadedFonts.has(family) || !window.FontFace) return Promise.resolve();

            loadedFonts.add(family);
            return new FontFace(family, 'url("' + url + '")').load()
                .then(font => document.fonts.add(font))
                .catch(() => {}); // A system fallback keeps the preview usable.
        }

        function redrawPreview() {
            if (!previews.some(preview => preview.background.complete && preview.background.naturalWidth)) return;

            const fontLoads = [];
            visibleRows().each(function () {
                fontLoads.push(loadFont($(this).find('[name="configuration[font_id][]"] option:selected')));
            });
            Promise.all(fontLoads).then(() => previews.forEach(drawTextConfigurations));
        }

        function drawTextConfigurations(preview) {
            const { canvas, context, background } = preview;
            if (!background.complete || !background.naturalWidth) return;
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(background, 0, 0, canvas.width, canvas.height);
            context.textBaseline = 'alphabetic';

            visibleRows().each(function () {
                const $row = $(this);
                const xValue = inputInRow($row, 'x_coordinate').val();
                const yValue = inputInRow($row, 'y_coordinate').val();
                const x = Number(xValue);
                const y = Number(yValue);
                const text = resolvePreviewVariables(inputInRow($row, 'text').val());
                const size = Number(inputInRow($row, 'font_size').val()) || 25;
                const angle = Number(inputInRow($row, 'font_angle').val()) || 0;
                const color = inputInRow($row, 'font_color_in_hexadecimal').val() || '#000000';
                const family = $row.find('[name="configuration[font_id][]"] option:selected').data('font-family') || 'sans-serif';

                if (!text || xValue === '' || yValue === '' || !Number.isFinite(x) || !Number.isFinite(y)) return;

                context.save();
                context.translate(x, y);
                // GD uses counter-clockwise angles; canvas' positive angles are clockwise.
                context.rotate(-angle * Math.PI / 180);
                context.font = size + 'px "' + family + '", sans-serif';
                context.fillStyle = color;
                context.fillText(text, 0, 0);
                context.restore();
            });
        }

        function resolvePreviewVariables(text) {
            let resolvedText = replaceConditionalFunctions(String(text));

            return resolvedText.replace(/%[A-Z0-9_?]+%/gi, variable => variableValue(variable) ?? 'Unknown');
        }

        function refreshPreviewVariables() {
            if (!previewVariablesUrl) return;

            fetch(previewVariablesUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
                .then(response => response.ok ? response.json() : Promise.reject())
                .then(variables => {
                    previewVariables = variables;
                    redrawPreview();
                })
                .catch(() => {});
        }

        function variableValue(variable) {
            const key = variable.slice(1, -1).toUpperCase();
            return Object.prototype.hasOwnProperty.call(previewVariables, key) ? String(previewVariables[key]) : null;
        }

        function replaceConditionalFunctions(text) {
            const functionPattern = /\$(ifset|default|if)\(/i;
            let match;
            while ((match = functionPattern.exec(text))) {
                const openingParenthesis = match.index + match[0].length - 1;
                const closingParenthesis = findClosingParenthesis(text, openingParenthesis);
                if (closingParenthesis === null) {
                    return text.slice(0, match.index) + 'Unknown' + text.slice(openingParenthesis + 1);
                }

                const argumentsList = splitFunctionArguments(text.slice(openingParenthesis + 1, closingParenthesis));
                const replacement = evaluateConditionalFunction(match[1].toLowerCase(), argumentsList);
                text = text.slice(0, match.index) + replacement + text.slice(closingParenthesis + 1);
            }

            return text;
        }

        function findClosingParenthesis(text, openingParenthesis) {
            let depth = 0;
            let quote = null;
            for (let position = openingParenthesis; position < text.length; position++) {
                const character = text[position];
                if (quote !== null) {
                    if (character === '\\') position++;
                    else if (character === quote) quote = null;
                    continue;
                }
                if (character === '"' || character === "'") quote = character;
                else if (character === '(') depth++;
                else if (character === ')' && --depth === 0) return position;
            }

            return null;
        }

        function splitFunctionArguments(argumentsText) {
            const argumentsList = [];
            let start = 0;
            let depth = 0;
            let quote = null;
            for (let position = 0; position < argumentsText.length; position++) {
                const character = argumentsText[position];
                if (quote !== null) {
                    if (character === '\\') position++;
                    else if (character === quote) quote = null;
                    continue;
                }
                if (character === '"' || character === "'") quote = character;
                else if (character === '(') depth++;
                else if (character === ')') depth--;
                else if (character === ',' && depth === 0) {
                    argumentsList.push(argumentsText.slice(start, position).trim());
                    start = position + 1;
                }
            }

            if (quote !== null || depth !== 0) return null;
            argumentsList.push(argumentsText.slice(start).trim());
            return argumentsList;
        }

        function evaluateConditionalFunction(name, argumentsList) {
            if (!argumentsList
                || (name === 'if' && argumentsList.length !== 3)
                || (name === 'default' && argumentsList.length !== 2)
                || (name === 'ifset' && ![2, 3].includes(argumentsList.length))) return 'Unknown';

            if (name === 'if') {
                const condition = evaluateCondition(argumentsList[0]);
                return condition === null ? 'Unknown' : unquote(condition ? argumentsList[1] : argumentsList[2]);
            }

            if (!/^%[A-Z0-9_?]+%$/i.test(argumentsList[0])) return 'Unknown';
            const value = variableValue(argumentsList[0]);
            const isSet = value !== null && value.trim() !== '';
            if (name === 'default') return unquote(isSet ? value : argumentsList[1]);

            return unquote(isSet ? argumentsList[1] : (argumentsList[2] ?? ''));
        }

        function evaluateCondition(condition) {
            const match = condition.match(/^\s*(%[A-Z0-9_?]+%|-?(?:\d+(?:\.\d*)?|\.\d+))\s*(<=|>=|==|!=|<|>)\s*(%[A-Z0-9_?]+%|-?(?:\d+(?:\.\d*)?|\.\d+))\s*$/i);
            if (!match) return null;
            const left = numericConditionValue(match[1]);
            const right = numericConditionValue(match[3]);
            if (left === null || right === null) return null;

            return { '<': left < right, '<=': left <= right, '>': left > right, '>=': left >= right, '==': left === right, '!=': left !== right }[match[2]];
        }

        function numericConditionValue(value) {
            const resolvedValue = value.startsWith('%') ? variableValue(value) : value;
            return resolvedValue !== null && resolvedValue !== '' && !Number.isNaN(Number(resolvedValue)) ? Number(resolvedValue) : null;
        }

        function unquote(value) {
            const trimmedValue = String(value).trim();
            if (trimmedValue.length >= 2 && ['"', "'"].includes(trimmedValue[0]) && trimmedValue[0] === trimmedValue.at(-1)) {
                return trimmedValue.slice(1, -1).replace(/\\(.)/g, '$1');
            }

            return trimmedValue;
        }

        $form.on('focusin click', '.banner-config-row :input', function () {
            selectActiveRow($(this).closest('.banner-config-row'));
        });
        $form.on('input change', '.banner-config-row :input', redrawPreview);

        previews.forEach(function ({ canvas }) {
            canvas.addEventListener('click', function (event) {
                const $row = activeRow && activeRow.length ? activeRow : visibleRows().first();
                if (!$row.length) return;

                selectActiveRow($row);
                const bounds = canvas.getBoundingClientRect();
                const x = Math.max(0, Math.min(canvas.width - 1, Math.floor((event.clientX - bounds.left) * canvas.width / bounds.width)));
                const y = Math.max(0, Math.min(canvas.height, Math.floor((event.clientY - bounds.top) * canvas.height / bounds.height)));
                inputInRow($row, 'x_coordinate').val(x).trigger('input');
                inputInRow($row, 'y_coordinate').val(y).trigger('input');
                $('input[name=x_coordinate_preview]').val(x);
                $('input[name=y_coordinate_preview]').val(y);
            });
        });

        // Hidden draft rows must not be submitted or block validation.
        $('div.d-none > div > div > input').each(function () {
            $(this).removeAttr('name').prop('required', false);
        });

        $('#add-config-row').click(function () {
            const $configRow = $('[id^="new-config-row"]:last');
            if ($configRow.hasClass('d-none')) {
                $configRow.find('input, select').each(function () {
                    $(this).prop('required', true);
                    $(this).attr('name', 'configuration[' + convertInputIdToSnakeCaseInputName($(this).attr('id')) + '][]');
                });
                $configRow.removeClass('d-none');
                selectActiveRow($configRow);
            } else {
                const nextNumber = parseInt($configRow.prop('id').match(/\d+/g), 10) + 1;
                const $clone = $configRow.clone().prop('id', 'new-config-row-' + nextNumber);
                $configRow.after($clone);
                selectActiveRow($clone);
            }
            redrawPreview();
        });

        $(document).on('click', '#remove-config-row', function () {
            const $row = $(this).closest('[id^="new-config-row"]');
            if (activeRow && activeRow.is($row)) activeRow = null;
            $row.remove();
            redrawPreview();
        });

        function convertInputIdToSnakeCaseInputName(inputIdValue) {
            const withoutPrefix = inputIdValue.replace(/validation/, '');
            return withoutPrefix.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`).replace(/^\_/, '');
        }

        selectActiveRow(visibleRows().first());
        refreshPreviewVariables();
        window.addEventListener('focus', refreshPreviewVariables);
        window.setInterval(refreshPreviewVariables, 15000);
    });
</script>
