<?php

namespace ERROPiX\AdvancedScripts;

/**
 * Class FreemiusTweaker
 * @package ERROPiX\AdvancedScripts
 */
class FreemiusTweaker
{
    private $hookname;
    private $accountTitle;

    public function __construct($freemius, $hookname, $accountTitle)
    {
        $this->hookname = $hookname;
        $this->accountTitle = $accountTitle;

        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        $freemius->add_filter('hide_account_tabs', [$this, 'replace_account_tabs']);
        $freemius->add_filter('show_affiliate_program_notice', '__return_false');
    }

    /**
     * Override Freemius account page tabs with normal heading title
     * 
     * @return boolean
     */
    public function replace_account_tabs()
    {
        echo "<h1>{$this->accountTitle}</h1>";
        return true;
    }

    /**
     * Enqueue JS/CSS code to hide sensible user details
     */
    public function admin_enqueue_scripts($hookname)
    {
        if (empty($_GET['unrestricted']) && $hookname == $this->hookname . '-account') {
            add_action("admin_head", function () {
                echo "<style>";
                echo "#pframe,";
                echo "#fs_account .postbox:not(:first-child),";
                echo "#fs_account .fs-header-actions a[href='#fs_billing'],";
                echo "#fs_account_details .fs-field-user_name form,";
                echo "#fs_account_details .fs-field-email form,";
                echo "#fs_account_details .fs-field-site_public_key,";
                echo "#fs_account_details .fs-field-site_secret_key,";
                echo "#fs_account_details .fs-field-plan .button-group,";
                echo "#fs_account_details .fs-field-license_key .fs-toggle-visibility,";
                echo "#fs_account_details .fs-field-license_key button {";
                echo "display: none;";
                echo "}";
                echo "</style>";
            });
        }
    }
}
