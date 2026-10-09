/**
 * Customizer: footer social links repeater (inc/class-social-links-control.php).
 * Each row is { icon: attachment ID, label, url }, saved as JSON in the setting.
 */
(function (api, $) {
    api.controlConstructor.awards_social_links = api.Control.extend({
        ready: function () {
            var control = this;
            var icons = control.params.icons || {};
            var $rows = control.container.find('.ag-social-rows');
            var rows;

            try {
                rows = JSON.parse(control.setting.get()) || [];
            } catch (e) {
                rows = [];
            }

            function save() {
                control.setting.set(JSON.stringify(rows));
            }

            function render() {
                $rows.empty();
                rows.forEach(function (row, i) {
                    var src = row.icon && icons[row.icon];
                    var $row = $('<div class="ag-social-row" style="border:1px solid #ddd;background:#fff;padding:10px;margin-bottom:10px;"></div>');

                    var $icon = $('<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;"></div>');
                    $icon.append(src
                        ? $('<img alt="" style="width:32px;height:32px;object-fit:contain;background:#1a1a1a;border-radius:50%;padding:6px;">').attr('src', src)
                        : $('<span style="width:44px;height:44px;border:1px dashed #bbb;border-radius:50%;display:inline-block;"></span>'));
                    $('<button type="button" class="button button-small"></button>')
                        .text(src ? 'Change icon' : 'Upload icon')
                        .on('click', function () { pickIcon(i); })
                        .appendTo($icon);
                    if (src) {
                        $('<button type="button" class="button-link button-link-delete">Remove icon</button>')
                            .on('click', function () { row.icon = 0; save(); render(); })
                            .appendTo($icon);
                    }
                    $row.append($icon);

                    $row.append(field('Label', 'text', row.label, function (v) { row.label = v; }));
                    $row.append(field('URL (empty hides it)', 'url', row.url, function (v) { row.url = v; }));

                    var $actions = $('<div style="display:flex;gap:8px;justify-content:space-between;"></div>');
                    var $move = $('<span></span>');
                    if (i > 0) {
                        $('<button type="button" class="button button-small" aria-label="Move up">&uarr;</button>')
                            .on('click', function () { move(i, i - 1); })
                            .appendTo($move);
                    }
                    if (i < rows.length - 1) {
                        $('<button type="button" class="button button-small" aria-label="Move down">&darr;</button>')
                            .on('click', function () { move(i, i + 1); })
                            .appendTo($move);
                    }
                    $actions.append($move);
                    $('<button type="button" class="button-link button-link-delete">Remove link</button>')
                        .on('click', function () { rows.splice(i, 1); save(); render(); })
                        .appendTo($actions);
                    $row.append($actions);

                    $rows.append($row);
                });
            }

            function field(label, type, value, onInput) {
                var $label = $('<label style="display:block;margin-bottom:8px;"></label>').text(label);
                $('<input class="widefat" style="margin-top:4px;">')
                    .attr('type', type)
                    .val(value || '')
                    .on('input', function () { onInput(this.value); save(); })
                    .appendTo($label);
                return $label;
            }

            function move(from, to) {
                rows.splice(to, 0, rows.splice(from, 1)[0]);
                save();
                render();
            }

            function pickIcon(i) {
                var frame = wp.media({
                    title: 'Select social icon',
                    library: { type: 'image' },
                    button: { text: 'Use this icon' },
                    multiple: false
                });
                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    var sizes = attachment.sizes || {};
                    icons[attachment.id] = (sizes.thumbnail || sizes.full || attachment).url;
                    rows[i].icon = attachment.id;
                    save();
                    render();
                });
                frame.open();
            }

            control.container.find('.ag-social-add').on('click', function () {
                rows.push({ icon: 0, label: '', url: '' });
                save();
                render();
            });

            render();
        }
    });
})(wp.customize, jQuery);
