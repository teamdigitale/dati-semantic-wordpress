<div class="wrap" id="advanced-scripts-container">
    <div class="app-container">
        <main class="app" id="advanced-scripts-app">
            <form class="app-form" id="advanced-scripts-form" action="<?= admin_url('admin-ajax.php') ?>" method="POST">
                <header class="header">
                    <div class="header-brand">
                        <strong class="header-title">Advanced Scripts</strong>
                    </div>

                    <div class="app-actions">
                        <button class="material-button" type="button" data-action="showColorPalette" title="Insert color" disabled>
                            <?= $this->icon("color-picker", 18, 18) ?>
                        </button>
                        <button class="material-button" type="button" data-action="toggleFullscreen" title="Fullscreen">
                            <?= $this->icon("fullscreen", 18, 18) ?>
                        </button>
                        <button class="material-button" type="button" data-action="toggleScriptsList" title="Toggle scripts list">
                            <?= $this->icon("chevron-right", 18, 18, "toggle-scripts-list") ?>
                        </button>
                    </div>

                    <div class="app-actions-popups">
                        <!-- Color palette -->
                        <div class="popper-dialog hidden" id="advanced-script-palette">
                            <div class="popper-dialog-body">
                                <?php foreach ($colors as $set_name => $set_colors) : ?>
                                    <?php if (!empty($set_colors)) : ?>
                                        <h3><?= $set_name ?></h3>
                                        <div class="colors">
                                            <?php foreach ($set_colors as $color) : ?>
                                                <a class="color-swatch" data-action="insertPaletteColor" data-id="<?= $color['id'] ?>" title="<?= $color["name"] . ": " . $color["value"] ?>">
                                                    <span style="background-color: <?= $color['value'] ?>"></span>
                                                </a>
                                            <?php endforeach ?>
                                        </div>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </div>
                            <a class="material-button material-button-small popper-dialog-close"><?= $this->icon("close", 18, 18) ?></a>
                            <div data-popper-arrow></div>
                        </div>
                    </div>
                </header>

                <?php if ($this->safe_mode) : ?>
                    <div class="safemode-notice-bar">
                        <?= $this->icon("shield", 18, 18) ?>
                        <span>The safe mode is active! PHP scripts won't be executed within the admin area.</span>

                        <?php if (!defined('AS_SAFE_MODE')) : ?>
                            <a class="btn-disable-safemode" href="<?= $this->safe_mode_disable_url ?>">Deactivate</a>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <input type="hidden" name="action" value="<?= $this->action_save ?>">
                <input type="hidden" name="token" value="<?= wp_create_nonce($this->action_save) ?>">
                <input type="hidden" name="id" value="<?= $edit_script['term_id'] ?>">
                <input type="hidden" name="parent" value="<?= $parent ?>">
                <input type="hidden" name="status" value="<?= $edit_script['status'] ?>">

                <div class="form-content">
                    <div class="script-fields">
                        <div class="form-controls">
                            <div class="form-control">
                                <label for="script-title">Title</label>
                                <input type="text" id="script-title" name="title" value="<?= esc_attr($edit_script["title"]) ?>" required>
                            </div>

                            <div class="form-control">
                                <label for="script-description">Description</label>
                                <textarea id="script-description" name="description" data-autoheight><?= esc_html($edit_script["description"]) ?></textarea>
                            </div>

                            <div class="form-control">
                                <label for="script-type">Type</label>
                                <select id="script-type" name="type" class="selectize" required>
                                    <option value=""></option>
                                    <?= $this->html_options($this->types, $edit_script["type"]) ?>
                                </select>
                            </div>

                            <div class="form-control hidden">
                                <label for="script-import">Import Rule</label>
                                <?php if ($edit_script["partial_import"]) : ?>
                                    <a class="form-control-button" data-action="copy" data-target="script-import">Copy</a>
                                <?php endif ?>
                                <input type="text" id="script-import" value="<?= esc_attr($edit_script["partial_import"]) ?>" placeholder="Click save to see the import rule" readonly>
                            </div>

                            <div class="form-control hidden">
                                <label for="script-location">Location</label>
                                <select id="script-location" name="location" class="selectize" required>
                                    <option value=""></option>
                                    <?= $this->html_options($this->locations, $edit_script["location"]) ?>
                                </select>
                            </div>

                            <div class="form-control hidden">
                                <label for="script-hook">Hooks</label>
                                <input type="text" id="script-hook" name="hook" value="<?= $edit_script["hook"] ?>" required>
                            </div>

                            <div class="form-control hidden">
                                <label for="script-shortcode">Shortcode</label>
                                <input type="text" id="script-shortcode" name="shortcode" value="<?= $edit_script["shortcode"] ?>">
                            </div>

                            <div class="form-control hidden">
                                <label for="script-priority">Priority</label>
                                <input type="number" id="script-priority" name="priority" value="<?= $edit_script["priority"] ?>">
                            </div>

                            <div class="form-control hidden">
                                <label for="script-url">URL</label>
                                <a class="form-control-button" data-action="uploadScriptFile">Upload</a>
                                <textarea id="script-url" name="url" data-autoheight required><?= $edit_script["url"] ?></textarea>
                            </div>

                            <div class="form-control hidden">
                                <label>Conditions</label>
                                <div class="input input-conditions-summary empty">
                                    <input type="hidden" id="script-conditions" name="conditions" value="<?= $edit_script["conditions"] ?>">
                                    <span class="summary"></span>
                                    <a class="toggle-conditions" data-action="toggleConditions"><?= $this->icon("chevron-right", 20, 20) ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="button button-primary" id="advanced-scripts-save">Save</button>
                            <button class="button button-secondary hidden" id="script-save-toggle" data-action="toggleFormStatus">
                                Save and <span class="toggle-status-action"><?= $edit_script['status'] ? "Deactivate" : "Activate" ?></span>
                            </button>
                        </div>
                    </div>

                    <div class="script-conditions">
                        <div id="condition-builder"></div>
                    </div>

                    <div class="script-editor">
                        <textarea id="script-code" name="content" style="display: none;"><?= htmlspecialchars($edit_script["code"]) ?></textarea>
                        <div id="ace-editor"></div>
                    </div>
                </div>
            </form>
            <aside class="app-list" id="advanced-scripts-list">
                <div class="app-list-wrap">
                    <header class="header">
                        <div class="list-actions">
                            <div class="list-bulk-actions">
                                <label class="checkbox" title="Select All">
                                    <input type="checkbox" id="list-bulk-select" value="all">
                                    <?= $this->icon("checkbox", 18, 18) ?>
                                </label>

                                <button class="material-button" type="button" data-action="exportScripts" title="Export" disabled>
                                    <?= $this->icon("export", 18, 18) ?>
                                </button>
                                <button class="material-button" type="button" data-action="deleteScripts" title="Delete" disabled>
                                    <?= $this->icon("delete", 18, 18) ?>
                                </button>
                            </div>

                            <div class="list-global-actions">
                                <button class="material-button" type="button" data-action="showImport" title="Import scripts">
                                    <?= $this->icon("import", 18, 18) ?>
                                </button>

                                <button class="material-button" type="button" data-action="showFolderEditor" title="Add new folder">
                                    <?= $this->icon("folder-plus", 18, 18) ?>
                                </button>

                                <a class="material-button" href="<?= $base_url ?>" title="Add new script">
                                    <?= $this->icon("plus", 18, 18) ?>
                                </a>
                            </div>

                            <div class="list-actions-popups"></div>
                        </div>
                    </header>

                    <div class="list-container">
                        <?php if ($parent) : ?>
                            <div class="list-path">
                                <a href="<?= $this->admin_url ?>" class="list-path-folder" data-id="0">
                                    <?= $this->icon("home", 18, 18) ?>
                                    <div class="item-dropzone"></div>
                                </a>

                                <?php if ($path_items && is_array($path_items)) : ?>
                                    <?php foreach ($path_items as $path) : ?>
                                        <?= $this->icon("chevron-right", 16, 16, "list-path-separator") ?>
                                        <?php if ($path["id"] != $parent) : ?>
                                            <a href="<?= $path["link"] ?>" class="list-path-folder" data-id="<?= $path["id"] ?>">
                                                <?= $this->icon("folder", 18, 18) ?> <?= $path["title"] ?>
                                                <div class="item-dropzone"></div>
                                            </a>
                                        <?php else : ?>
                                            <span class="list-path-folder"><?= $this->icon("folder", 18, 18) ?><?= $path["title"] ?></span>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </div>
                        <?php endif ?>

                        <div id="advanced-script-import">
                            <div class="file-dropzone">
                                <?= $this->icon("json-file", 48, 48); ?>
                                <p>Drag & Drop your exported JSON files here to import them.</p>
                                <p>(Accept <b>Code Snippets</b> files)</p>

                                <div class="radiobox-group">
                                    <?= $this->html_radiobox("status", -1, false, "Original") ?>
                                    <?= $this->html_radiobox("status", 0, true, "Disabled") ?>
                                    <?= $this->html_radiobox("status", 1, false, "Enabled") ?>
                                </div>
                            </div>

                            <a class="material-button material-button-small" data-action="hideImport"><?= $this->icon("close", 18, 18) ?></a>
                        </div>

                        <div class="list-items">
                            <?php foreach ($scripts as $script) : ?>
                                <?php
                                $term_id = $script["term_id"];
                                $is_folder = $script["type"] == "folder";
                                $is_partial = $script["type"] == "text/x-scss-partial";
                                $title = $script["title"] ?: "[Untitled]";

                                $classes = ["list-item"];
                                if ($is_folder) {
                                    $classes[] = "list-folder";
                                    $query = [
                                        "parent" => $term_id,
                                    ];
                                } else {
                                    $query = [
                                        "edit" => $term_id,
                                    ];
                                }

                                if ($script["conditions"]) {
                                    $classes[] = "has-conditions";
                                }

                                if ($term_id == $edit_id) {
                                    $classes[] = "active";
                                }
                                ?>
                                <div class="<?= implode(" ", $classes) ?>" data-id="<?= $term_id ?>">
                                    <!-- <div class="list-item-header"> -->
                                    <div class="item-checkbox">
                                        <?= $this->html_checkbox("script[$term_id]", $term_id) ?>
                                    </div>
                                    <div class="item-icon">
                                        <?= $this->icon($script["icon"], 18, 18) ?>
                                    </div>
                                    <div class="item-title">
                                        <a href="<?= add_query_arg($query, $base_url) ?>"><?= $title ?></a>
                                    </div>
                                    <?php if ($script["description"]) : ?>
                                        <a class="item-description" title="<?= esc_attr($script["description"]) ?>">
                                            <?= $this->icon("comment", 18, 18) ?>
                                        </a>
                                    <?php endif ?>
                                    <?php if ($is_folder) : ?>
                                        <a class="item-edit material-button material-button-small" data-action="showFolderEditor" title="Rename folder">
                                            <?= $this->icon("edit", 18, 18) ?>
                                        </a>
                                    <?php endif ?>
                                    <?php if (!$is_partial) : ?>
                                        <div class="item-status">
                                            <?= $this->html_switchbox("status", $term_id, $script["status"]) ?>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($is_folder) : ?>
                                        <div class="item-dropzone"></div>
                                    <?php endif ?>
                                    <!-- </div> -->
                                </div>
                            <?php endforeach ?>

                            <div id="advanced-script-folder-editor" class="hidden">
                                <div class="folder-wrapper">
                                    <div class="folder-icon">
                                        <?= $this->icon("folder", 18, 18) ?>
                                    </div>

                                    <div class="folder-title">
                                        <input type="text" name="title" placeholder="New folder name">
                                    </div>

                                    <button class="material-button material-button-small" data-action="saveFolder"><?= $this->icon("check", 18, 18) ?></button>
                                    <button class="material-button material-button-small" data-action="hideFolderEditor"><?= $this->icon("close", 18, 18) ?></button>

                                    <div class="folder-status">
                                        <?= $this->html_switchbox("status", "") ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </main>

        <div class="popper-dialog-overlay"></div>
    </div>
</div>

<script type="text/html" id="tmpl-condition-builder-rule-field">
    <div class="form-control <%= className %>">
        <input type="text" class="<%= className %>-input">
    </div>
</script>

<script type="text/html" id="tmpl-condition-builder-rule-value-range">
    <div class="form-control form-control-start">
        <input type="text" placeholder="first value" value="<%= value[0] %>">
    </div>
    <div class="form-control form-control-end">
        <input type="text" placeholder="second value" value="<%= value[1] %>">
    </div>
</script>

<script type="text/html" id="tmpl-condition-builder-rule-actions">
    <span class="rule-error"><%= error %></span>

    <button class="material-button action-move-up <%= isFirst && 'disabled' %>" type="button"><?= $this->icon("arrow-up", 18, 18) ?></button>
    <button class="material-button action-move-down <%= isLast && 'disabled' %>" type="button"><?= $this->icon("arrow-down", 18, 18) ?></button>
    <button class="material-button action-delete" type="button"><?= $this->icon("close", 18, 18) ?></button>
</script>

<script type="text/html" id="tmpl-condition-builder-header">
    <% if (relation == "and") { %>
    Execute if <a>all</a> conditions are true
    <% } else { %>
    Execute if <a>any</a> condition is true
    <% } %>
</script>

<script type="text/html" id="tmpl-condition-builder-footer">
    <button class="button button-secondary button-add-rule" type="button">Add New</button>
</script>
