<?php

use ERROPiX\AdvancedScripts\ScriptsManager;
use ERROPiX\AdvancedScripts\ConditionManager;
use ERROPiX\AdvancedScripts\SiteHealth;
use ERROPiX\AdvancedScripts\Storage;
/**
 * @return ScriptsManager 
 */
function cpas_scripts_manager() {
    static $instance = null;
    if ( $instance == null ) {
        $instance = new ScriptsManager();
        $instance->init();
    }
    return $instance;
}

/**
 * @return ConditionManager 
 */
function cpas_condition_manager() {
    static $instance = null;
    if ( $instance == null ) {
        $instance = new ConditionManager();
    }
    return $instance;
}

/**
 * @return SiteHealth
 */
function cpas_site_health() {
    static $instance = null;
    if ( $instance == null ) {
        $instance = new SiteHealth();
    }
    return $instance;
}

/**
 * @return Storage
 */
function cpas_storage() {
    static $instance = null;
    if ( $instance == null ) {
        $instance = new Storage();
    }
    return $instance;
}

/**
 * @param array|null $script
 * 
 * @return array|null
 */
function cpas_current_script(  $script = null  ) {
    static $current_script = null;
    if ( $script !== null ) {
        $current_script = ( $script ?: null );
    }
    return $current_script;
}

/**
 * Check if a string matches a given minimatch pattern.
 *
 * @see cpas_minimatch_to_regex() for supported patterns
 *
 * @param string $string  The string to check
 * @param string $pattern The minimatch pattern
 *
 * @return bool Whether the string matches the given pattern.
 */
function cpas_match(  $string, $pattern  ) {
    // Convert minimatch pattern to regex.
    $regex = '^' . cpas_minimatch_to_regex( $pattern ) . '$';
    // Choose a delimiter based on the final regex
    $delimiter = cpas_regex_delimiter( $regex );
    // Build the full regex with the chosen delimiter
    $regex = $delimiter . $regex . $delimiter;
    $result = preg_match( $regex, $string );
    return $result === 1;
}

/**
 * Recursively converts a minimatch pattern into a regular expression.
 *
 * Supported patterns:
 * - '*' : One or more characters except '/'
 * - '**' : One or more characters, including '/'
 * - '?' : Exactly one character
 * - '+' : One or more occurrences of the preceding token or group
 * - '(foo|bar)' : One of the given options group
 *
 * @param string $pattern The minimatch pattern.
 * @param bool   $inGroup Whether the pattern is inside a group.
 *
 * @return string The regular expression (without delimiters or anchors).
 */
function cpas_minimatch_to_regex(  $pattern, $inGroup = false  ) {
    $regex = '';
    $len = strlen( $pattern );
    for ($i = 0; $i < $len; $i++) {
        $char = $pattern[$i];
        // Handle escapes: backslash means literal next character.
        if ( $char === '\\' ) {
            if ( $i + 1 < $len ) {
                $regex .= preg_quote( $pattern[++$i], '' );
            } else {
                $regex .= '\\\\';
            }
        } elseif ( $char === '*' ) {
            // Check if it's a "**" token.
            if ( $i + 1 < $len && $pattern[$i + 1] === '*' ) {
                $regex .= '.+';
                $i++;
                // consume the next '*'
            } else {
                $regex .= '[^/]+';
            }
        } elseif ( $char === '?' ) {
            $regex .= '.';
        } elseif ( $char === '+' ) {
            $regex .= '+';
        } elseif ( $char === '(' ) {
            $groupContent = '';
            $depth = 1;
            $i++;
            // skip the '('
            while ( $i < $len && $depth > 0 ) {
                if ( $pattern[$i] === '\\' ) {
                    if ( $i + 1 < $len ) {
                        // Preserve escapes inside groups.
                        $groupContent .= $pattern[$i] . $pattern[$i + 1];
                        $i += 2;
                        continue;
                    } else {
                        $groupContent .= '\\';
                        $i++;
                        continue;
                    }
                } elseif ( $pattern[$i] === '(' ) {
                    $depth++;
                    $groupContent .= '(';
                } elseif ( $pattern[$i] === ')' ) {
                    $depth--;
                    if ( $depth > 0 ) {
                        $groupContent .= ')';
                    }
                } else {
                    $groupContent .= $pattern[$i];
                }
                $i++;
            }
            // Process the group content recursively.
            $regex .= '(' . cpas_minimatch_to_regex( $groupContent, true ) . ')';
            // Adjust index: the outer loop will increment $i, so we decrement here to avoid skipping.
            $i--;
        } else {
            $escaped = preg_quote( $char, '' );
            if ( $inGroup && $char === '|' ) {
                $escaped = '|';
            }
            $regex .= $escaped;
        }
    }
    return $regex;
}

/**
 * Chooses a regex delimiter that does not occur in the given regex.
 *
 * @param string $regex The generated regex body.
 *
 * @return string A suitable regex delimiter.
 */
function cpas_regex_delimiter(  $regex  ) {
    $candidates = [
        '#',
        '@',
        '%',
        '~',
        '`',
        '!',
        ';',
        ':',
        '='
    ];
    foreach ( $candidates as $delimiter ) {
        if ( strpos( $regex, $delimiter ) === false ) {
            return $delimiter;
        }
    }
    // Fallback if all candidates appear in the regex.
    return '/';
}

function erropix_advanced_scripts_fs() {
    static $fs = null;
    if ( is_null( $fs ) ) {
        $fs = fs_dynamic_init( array(
            'id'              => '6334',
            'slug'            => 'erropix-advanced-scripts',
            'premium_slug'    => 'erropix-advanced-scripts',
            'type'            => 'plugin',
            'public_key'      => 'pk_7fa5d0ea8a6b33dc5813ac896c002',
            'has_addons'      => false,
            'is_premium'      => true,
            'is_premium_only' => true,
            'has_paid_plans'  => true,
            'trial'           => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
            'menu'            => array(
                'slug'    => 'advanced-scripts',
                'account' => false,
                'contact' => false,
                'support' => false,
                'parent'  => array(
                    'slug' => 'tools.php',
                ),
            ),
            'is_live'         => true,
        ) );
    }
    return $fs;
}
