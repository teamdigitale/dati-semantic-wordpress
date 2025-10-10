<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Oxy_Toolbox_Conditions {

	static $prefix;
	static $mod         = 'conditions_';
	static $title       = 'Conditions';
	static $description = 'Adds additional conditions in the Oxygen editor.';
	static $link        = 'https://oxyplugins.com/doc/conditions/';

	static $associative_array;

	static $languages_array = array(
		'af'     => 'Afrikaans : af',
		'af-ZA'  => 'Afrikaans (South Africa) : af-ZA',
		'sq'     => 'Albanian : sq',
		'sq-AL'  => 'Albanian (Albania) : sq-AL',
		'ar'     => 'Arabic : ar',
		'ar-AE'  => 'Arabic (U.A.E.) : ar-AE',
		'ar-BH'  => 'Arabic (Bahrain) : ar-BH',
		'ar-DZ'  => 'Arabic (Algeria) : ar-DZ',
		'ar-EG'  => 'Arabic (Egypt) : ar-EG',
		'ar-IQ'  => 'Arabic (Iraq) : ar-IQ',
		'ar-JO'  => 'Arabic (Jordan) : ar-JO',
		'ar-KW'  => 'Arabic (Kuwait) : ar-KW',
		'ar-LB'  => 'Arabic (Lebanon) : ar-LB',
		'ar-LY'  => 'Arabic (Libya) : ar-LY',
		'ar-MA'  => 'Arabic (Morocco) : ar-MA',
		'ar-OM'  => 'Arabic (Oman) : ar-OM',
		'ar-QA'  => 'Arabic (Qatar) : ar-QA',
		'ar-SA'  => 'Arabic (Saudi Arabia) : ar-SA',
		'ar-SY'  => 'Arabic (Syria) : ar-SY',
		'ar-TN'  => 'Arabic (Tunisia) : ar-TN',
		'ar-YE'  => 'Arabic (Yemen) : ar-YE',
		'hy'     => 'Armenian : hy',
		'hy-AM'  => 'Armenian (Armenia) : hy-AM',
		'az'     => 'Azeri (Latin) : az',
		'az-AZ'  => 'Azeri (Latin) (Azerbaijan) : az-AZ',
		'az-AZ'  => 'Azeri (Cyrillic) (Azerbaijan) : az-AZ',
		'eu'     => 'Basque : eu',
		'eu-ES'  => 'Basque (Spain) : eu-ES',
		'be'     => 'Belarusian : be',
		'be-BY'  => 'Belarusian (Belarus) : be-BY',
		'bs-BA'  => 'Bosnian (Bosnia and Herzegovina) : bs-BA',
		'bg'     => 'Bulgarian : bg',
		'bg-BG'  => 'Bulgarian (Bulgaria) : bg-BG',
		'ca'     => 'Catalan : ca',
		'ca-ES'  => 'Catalan (Spain) : ca-ES',
		'zh'     => 'Chinese : zh',
		'zh-CN'  => 'Chinese (S) : zh-CN',
		'zh-HK'  => 'Chinese (Hong Kong) : zh-HK',
		'zh-MO'  => 'Chinese (Macau) : zh-MO',
		'zh-SG'  => 'Chinese (Singapore) : zh-SG',
		'zh-TW'  => 'Chinese (T) : zh-TW',
		'hr'     => 'Croatian : hr',
		'hr-BA'  => 'Croatian (Bosnia and Herzegovina) - hr-BA',
		'hr-HR'  => 'Croatian (Croatia) : hr-HR',
		'cs'     => 'Czech : cs',
		'cs-CZ'  => 'Czech (Czech Republic) : cs-CZ',
		'da'     => 'Danish : da',
		'da-DK'  => 'Danish (Denmark) : da-DK',
		'dv'     => 'Divehi : dv',
		'dv-MV'  => 'Divehi (Maldives) : dv-MV',
		'nl'     => 'Dutch : nl',
		'nl-BE'  => 'Dutch (Belgium) : nl-BE',
		'nl-NL'  => 'Dutch (Netherlands) : nl-NL',
		'en'     => 'English : en',
		'en-AU'  => 'English (Australia) : en-AU',
		'en-BZ'  => 'English (Belize) : en-BZ',
		'en-CA'  => 'English (Canada) : en-CA',
		'en-CB'  => 'English (Caribbean) : en-CB',
		'en-GB'  => 'English (United Kingdom) : en-GB',
		'en-IE'  => 'English (Ireland) : en-IE',
		'en-JM'  => 'English (Jamaica) : en-JM',
		'en-NZ'  => 'English (New Zealand) : en-NZ',
		'en-PH'  => 'English (Republic of the Philippines) : en-PH',
		'en-TT'  => 'English (Trinidad and Tobago) : en-TT',
		'en-US'  => 'English (United States) : en-US',
		'en-ZA'  => 'English (South Africa) : en-ZA',
		'en-ZW'  => 'English (Zimbabwe) : en-ZW',
		'eo'     => 'Esperanto : eo',
		'et'     => 'Estonian : et',
		'et-EE'  => 'Estonian (Estonia) : et-EE',
		'fo'     => 'Faroese : fo',
		'fo-FO'  => 'Faroese (Faroe Islands) : fo-FO',
		'fa'     => 'Farsi : fa',
		'fa-IR'  => 'Farsi (Iran) : fa-IR',
		'fi'     => 'Finnish : fi',
		'fi-FI'  => 'Finnish (Finland) : fi-FI',
		'fr'     => 'French : fr',
		'fr-BE'  => 'French (Belgium) : fr-BE',
		'fr-CA'  => 'French (Canada) : fr-CA',
		'fr-CH'  => 'French (Switzerland) : fr-CH',
		'fr-FR'  => 'French (France) : fr-FR',
		'fr-LU'  => 'French (Luxembourg) : fr-LU',
		'fr-MC'  => 'French (Principality of Monaco) : fr-MC',
		'mk'     => 'FYRO Macedonian : mk',
		'mk-MK'  => 'FYRO Macedonian (Former Yugoslav Republic of Macedonia) : mk-MK',
		'gl'     => 'Galician : gl',
		'gl-ES'  => 'Galician (Spain) : gl-ES',
		'ka'     => 'Georgian : ka',
		'ka-GE'  => 'Georgian (Georgia) : ka-GE',
		'de'     => 'German : de',
		'de-AT'  => 'German (Austria) : de-AT',
		'de-CH'  => 'German (Switzerland) : de-CH',
		'de-DE'  => 'German (Germany) : de-DE',
		'de-LI'  => 'German (Liechtenstein) : de-LI',
		'de-LU'  => 'German (Luxembourg) : de-LU',
		'el'     => 'Greek : el',
		'el-GR'  => 'Greek (Greece) : el-GR',
		'gu'     => 'Gujarati : gu',
		'gu-IN'  => 'Gujarati (India) : gu-IN',
		'he'     => 'Hebrew : he',
		'he-IL'  => 'Hebrew (Israel) : he-IL',
		'hi'     => 'Hindi : hi',
		'hi-IN'  => 'Hindi (India) : hi-IN',
		'hu'     => 'Hungarian : hu',
		'hu-HU'  => 'Hungarian (Hungary) : hu-HU',
		'is'     => 'Icelandic : is',
		'is-IS'  => 'Icelandic (Iceland) : is-IS',
		'id'     => 'Indonesian : id',
		'id-ID'  => 'Indonesian (Indonesia) : id-ID',
		'it'     => 'Italian : it',
		'it-CH'  => 'Italian (Switzerland) : it-CH',
		'it-IT'  => 'Italian (Italy) : it-IT',
		'ja'     => 'Japanese : ja',
		'ja-JP'  => 'Japanese (Japan) : ja-JP',
		'lv'     => 'Latvian : lv',
		'lv-LV'  => 'Latvian (Latvia) : lv-LV',
		'lt'     => 'Lithuanian : lt',
		'lt-LT'  => 'Lithuanian (Lithuania) : lt-LT',
		'kn'     => 'Kannada : kn',
		'kn-IN'  => 'Kannada (India) : kn-IN',
		'kk'     => 'Kazakh : kk',
		'kk-KZ'  => 'Kazakh (Kazakhstan) : kk-KZ',
		'kok'    => 'Konkani : kok',
		'kok-IN' => 'Konkani (India) : kok-IN',
		'ko'     => 'Korean : ko',
		'ko-KR'  => 'Korean (Korea) : ko-KR',
		'ky'     => 'Kyrgyz : ky',
		'ky-KG'  => 'Kyrgyz (Kyrgyzstan) : ky-KG',
		'ms'     => 'Malay : ms',
		'ms-BN'  => 'Malay (Brunei Darussalam) : ms-BN',
		'ms-MY'  => 'Malay (Malaysia) : ms-MY',
		'mt'     => 'Maltese : mt',
		'mt-MT'  => 'Maltese (Malta) : mt-MT',
		'mi'     => 'Maori : mi',
		'mi-NZ'  => 'Maori (New Zealand) : mi-NZ',
		'mr'     => 'Marathi : mr',
		'mr-IN'  => 'Marathi (India) : mr-IN',
		'mn'     => 'Mongolian : mn',
		'mn-MN'  => 'Mongolian (Mongolia) : mn-MN',
		'ns'     => 'Northern Sotho : ns',
		'ns-ZA'  => 'Northern Sotho (South Africa) : ns-ZA',
		'nb'     => 'Norwegian (Bokm?l) : nb',
		'nb-NO'  => 'Norwegian (Bokm?l) (Norway) : nb-NO',
		'nn-NO'  => 'Norwegian (Nynorsk) (Norway) : nn-NO',
		'ps'     => 'Pashto : ps',
		'ps-AR'  => 'Pashto (Afghanistan) : ps-AR',
		'pl'     => 'Polish : pl',
		'pl-PL'  => 'Polish (Poland) : pl-PL',
		'pt'     => 'Portuguese : pt',
		'pt-BR'  => 'Portuguese (Brazil) : pt-BR',
		'pt-PT'  => 'Portuguese (Portugal) : pt-PT',
		'pa'     => 'Punjabi : pa',
		'pa-IN'  => 'Punjabi (India) : pa-IN',
		'qu'     => 'Quechua : qu',
		'qu-BO'  => 'Quechua (Bolivia) : qu-BO',
		'qu-EC'  => 'Quechua (Ecuador) : qu-EC',
		'qu-PE'  => 'Quechua (Peru) : qu-PE',
		'ro'     => 'Romanian : ro',
		'ro-RO'  => 'Romanian (Romania) : ro-RO',
		'ru'     => 'Russian : ru',
		'ru-RU'  => 'Russian (Russia) : ru-RU',
		'se'     => 'Sami (Northern) : se',
		'se-FI'  => 'Sami (Northern) (Finland) : se-FI',
		'se-FI'  => 'Sami (Skolt) (Finland) : se-FI',
		'se-FI'  => 'Sami (Inari) (Finland) : se-FI',
		'se-NO'  => 'Sami (Northern) (Norway) : se-NO',
		'se-NO'  => 'Sami (Lule) (Norway) : se-NO',
		'se-NO'  => 'Sami (Southern) (Norway) : se-NO',
		'se-SE'  => 'Sami (Northern) (Sweden) : se-SE',
		'se-SE'  => 'Sami (Lule) (Sweden) : se-SE',
		'se-SE'  => 'Sami (Southern) (Sweden) : se-SE',
		'sa'     => 'Sanskrit : sa',
		'sa-IN'  => 'Sanskrit (India) : sa-IN',
		'sr-BA'  => 'Serbian (Latin) (Bosnia and Herzegovina) : sr-BA',
		'sr-BA'  => 'Serbian (Cyrillic) (Bosnia and Herzegovina) : sr-BA',
		'sr-SP'  => 'Serbian (Latin) (Serbia and Montenegro) : sr-SP',
		'sr-SP'  => 'Serbian (Cyrillic) (Serbia and Montenegro) : sr-SP',
		'sk'     => 'Slovak : sk',
		'sk-SK'  => 'Slovak (Slovakia) : sk-SK',
		'sl'     => 'Slovenian : sl',
		'sl-SI'  => 'Slovenian (Slovenia) : sl-SI',
		'es'     => 'Spanish : es',
		'es-AR'  => 'Spanish (Argentina) : es-AR',
		'es-BO'  => 'Spanish (Bolivia) : es-BO',
		'es-CL'  => 'Spanish (Chile) : es-CL',
		'es-CO'  => 'Spanish (Colombia) : es-CO',
		'es-CR'  => 'Spanish (Costa Rica) : es-CR',
		'es-DO'  => 'Spanish (Dominican Republic) : es-DO',
		'es-EC'  => 'Spanish (Ecuador) : es-EC',
		'es-ES'  => 'Spanish (Castilian) : es-ES',
		'es-ES'  => 'Spanish (Spain) : es-ES',
		'es-GT'  => 'Spanish (Guatemala) : es-GT',
		'es-HN'  => 'Spanish (Honduras) : es-HN',
		'es-MX'  => 'Spanish (Mexico) : es-MX',
		'es-NI'  => 'Spanish (Nicaragua) : es-NI',
		'es-PA'  => 'Spanish (Panama) : es-PA',
		'es-PE'  => 'Spanish (Peru) : es-PE',
		'es-PR'  => 'Spanish (Puerto Rico) : es-PR',
		'es-PY'  => 'Spanish (Paraguay) : es-PY',
		'es-SV'  => 'Spanish (El Salvador) : es-SV',
		'es-UY'  => 'Spanish (Uruguay) : es-UY',
		'es-VE'  => 'Spanish (Venezuela) : es-VE',
		'sw'     => 'Swahili : sw',
		'sw-KE'  => 'Swahili (Kenya) : sw-KE',
		'sv'     => 'Swedish : sv',
		'sv-FI'  => 'Swedish (Finland) : sv-FI',
		'sv-SE'  => 'Swedish (Sweden) : sv-SE',
		'syr'    => 'Syriac : syr',
		'syr-SY' => 'Syriac (Syria) : syr-SY',
		'tl'     => 'Tagalog : tl',
		'tl-PH'  => 'Tagalog (Philippines) : tl-PH',
		'tt'     => 'Tatar : tt',
		'tt-RU'  => 'Tatar (Russia) : tt-RU',
		'ta'     => 'Tamil : ta',
		'ta-IN'  => 'Tamil (India) : ta-IN',
		'te'     => 'Telugu : te',
		'te-IN'  => 'Telugu (India) : te-IN',
		'th'     => 'Thai : th',
		'th-TH'  => 'Thai (Thailand) : th-TH',
		'ts'     => 'Tsonga : ts',
		'tn'     => 'Tswana : tn',
		'tn-ZA'  => 'Tswana (South Africa) : tn-ZA',
		'tr'     => 'Turkish : tr',
		'tr-TR'  => 'Turkish (Turkey) : tr-TR',
		'uk'     => 'Ukrainian : uk',
		'uk-UA'  => 'Ukrainian (Ukraine) : uk-UA',
		'ur'     => 'Urdu : ur',
		'ur-PK'  => 'Urdu (Islamic Republic of Pakistan) : ur-PK',
		'uz'     => 'Uzbek (Latin) : uz',
		'uz-UZ'  => 'Uzbek (Latin) (Uzbekistan) : uz-UZ',
		'uz-UZ'  => 'Uzbek (Cyrillic) (Uzbekistan) : uz-UZ',
		'vi'     => 'Vietnamese : vi',
		'vi-VN'  => 'Vietnamese (Viet Nam) : vi-VN',
		'cy'     => 'Welsh : cy',
		'cy-GB'  => 'Welsh (United Kingdom) : cy-GB',
		'xh'     => 'Xhosa : xh',
		'xh-ZA'  => 'Xhosa (South Africa) : xh-ZA',
		'zu'     => 'Zulu : zu',
		'zu-ZA'  => 'Zulu (South Africa) : zu-ZA',
	);

	static $countries = array(
		'GB' => 'United Kingdom (UK)',
		'US' => 'United States (US)',
		'AU' => 'Australia',
		'CA' => 'Canada',
		'FR' => 'France',
		'DE' => 'Germany',
		'IN' => 'India',
		'AF' => 'Afghanistan',
		'AX' => 'Åland Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'PW' => 'Belau',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BQ' => 'Bonaire, Saint Eustatius and Saba',
		'BA' => 'Bosnia and Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo (Brazzaville)',
		'CD' => 'Congo (Kinshasa)',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CW' => 'Curaçao',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island and McDonald Islands',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'ID' => 'Indonesia',
		'IR' => 'Iran',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'CI' => 'Ivory Coast',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Laos',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'North Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'KP' => 'North Korea',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PS' => 'Palestinian Territory',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russia',
		'RW' => 'Rwanda',
		'BL' => 'Saint-Barthélemy',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts and Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin (French part)',
		'SX' => 'Saint Martin (Dutch part)',
		'PM' => 'Saint Pierre and Miquelon',
		'VC' => 'Saint Vincent and the Grenadines',
		'SM' => 'San Marino',
		'ST' => 'São Tomé e Príncipe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia/Sandwich Islands',
		'KR' => 'South Korea',
		'SS' => 'South Sudan',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard and Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syria',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'UM' => 'United States (US) Minor Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VA' => 'Vatican',
		'VE' => 'Venezuela',
		'VN' => 'Vietnam',
		'VG' => 'Virgin Islands (British)',
		'VI' => 'Virgin Islands (US)',
		'WF' => 'Wallis and Futuna',
		'EH' => 'Western Sahara',
		'WS' => 'Samoa',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);

	static $option_names = array(
		'wlm_memberships', // WLM Membership(s)
		'post_id_in_array', // Post ID in Array
		'us_state', // US State
		'post_password_protected', // Post Password Protected
		'at_least_one_search_result', // At Least 1 Search Result
		'cpt_has_entry', // CPT has at least 1 published entry
		'mobile_detect', // Mobile Detect
		'current_cpt_author', // Current CPT Author
		'is_homepage', // Is Homepage
		'author_has_cpt_entry', // Author has CPT entry
		'is_child_of_specific_page', // Is Child of a Specific Page
		'is_child', // Is Child
		'country', // Country
		'month', // Month
		'referrer_url_parameter', // Referrer URL Parameter
		'archive_type', // Archive Type
		'memberpress_membership', // MemberPress Membership
		'rcp_membership', // RCP Membership
		'language', // Language
		'body_class', // Body Class
		'browser_detect', // Browser Detect
		'wc_conditions', // WooCommerce Conditions
		'is_affiliate_affiliatewp', // Is Affiliate (AffiliateWP)
		'http_referer', // HTTP Referer
		'is_archive', // Is Archive
		'post_has_tags', // Post Has Tags
		'published_during_last', // Post published during the last
		'polylang', // Polylang Locale
		'is_blog', // Is Blog
		'date_time', // Date and time
		'user_has_written',
		// 'user_has_written_public_only'
	);

	public static function init( $prefix ) {
		self::$prefix    = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		add_action( self::$prefix . 'register_options', array( __CLASS__, 'mod_register_options' ) );
		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		add_action( self::$prefix . self::$mod . 'form_options', array( __CLASS__, 'mod_form_options' ) );

		foreach ( self::$option_names as $option_name ) {
			if ( 'true' === get_option( self::$prefix . self::$mod . $option_name, false ) ) {
				add_action( 'init', array( __CLASS__, 'register_condition_' . $option_name ) );
			}
		}
	}

	/***************************************************************
	 * Registering the conditions
	 ***************************************************************/

	// Date and time
	public static function register_condition_date_time() {
		if ( function_exists( 'oxygen_vsb_register_condition') ) {
			
			oxygen_vsb_register_condition(
				// Condition Name
				'Date and time',
				
				// Values
				array( 
					'custom' => true,
					'placeholder' => 'Y-m-d H:i:s. Ex.: 2021-03-21 16:29:00'
				),

				// Operators
				array( 'is after', 'is before', '==' ),
				
				// Callback Function
				'oxy_toolbox_conditions_date_time_callback',
				
				// Condition Category
				'Other'
			);			
		}
	}
	
	// Is Blog
	public static function register_condition_is_blog() {
		if ( function_exists( 'oxygen_vsb_register_condition') ) {
			
			oxygen_vsb_register_condition(
				// Condition Name
				'Is Blog',
				
				// Values
				array( 
					'options' => array( 'true', 'false' ),
					'custom' => false
				),

				// Operators
				array( '==' ),
				
				// Callback Function
				'oxy_toolbox_conditions_is_blog_callback',
				
				// Condition Category
				'Other'
			);			
		}
	}
	
	// Post published during the last
	public static function register_condition_polylang() {
		if ( function_exists( 'oxygen_vsb_register_condition') && function_exists( 'pll_languages_list' ) ) {
			$lang_list = pll_languages_list();
			
			oxygen_vsb_register_condition(
				// Condition Name
				'Locale',
				
				// Values
				array( 
					'options' => $lang_list,
					'custom' => false
				),

				// Operators
				array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_polylang_callback',
				
				// Condition Category
				'Post'
			);			
		}
	}
	
	// Post published during the last
	public static function register_condition_published_during_last() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name.
				'Post published during the last',
		
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( '1 week', '1 month', '1 year' ),
					'custom'  => false,
				),
		
				// Operators.
				array( '==' ),
		
				// Callback Function: Name of function that will be used to handle the condition.
				'oxy_toolbox_conditions_published_during_last_callback',
		
				// Condition Category: Default ones are Archive, Author, Other, Post, User.
				'Post'
			);
		}
	}

	// WLM Membership(s).
	public static function register_condition_wlm_memberships() {
		// Retrieves all membership levels associated with WLM.
		// http://codex.wishlistproducts.com/function-reference-wlmapi_get_levels/
		if ( function_exists( 'wlmapi_get_levels' ) ) {
			$levels = wlmapi_get_levels();
		}

		// Empty array to store the names of membership levels.
		$level_names_array = array();

		// Empty array to store id=>level.
		self::$associative_array = array();

		// store all the level names in $level_names_array and all id=>level key=>value pairs in $associative_array.
		if( is_array( $levels['levels']['level'] ) ) {
			foreach ( $levels['levels']['level'] as $level ) {
				$level_names_array[]                     = $level['name'];
				self::$associative_array[ $level['id'] ] = $level['name'];
			}
		}

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'WLM Membership(s)',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => $level_names_array,
					'custom'  => false,
				),
				// Operators
				array( '==', '!=' ),
				// Callback Function: Name of function that will be used to handle the condition
				// the callback cannot be a member method of a class because in Oxygen's conditions API, the presence of callback is checked using function_exists, rather than method_exists
				'oxy_toolbox_conditions_wlm_membership_callback', // this is going to target the function that is out of this class, and defined at the bottom of the page.
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'User'
			);
		}
	}

	// Post ID in Array.
	public static function register_condition_post_id_in_array() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Post ID in Array',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => '',
					'custom'  => true,
				),
				// Operators
				array( 'in' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_post_id_in_array_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Post'
			);

		}
	}

	// US State.
	public static function register_condition_us_state() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			$us_states_list = array(
				'Alabama',
				'Alaska',
				'Arizona',
				'Arkansas',
				'California',
				'Colorado',
				'Connecticut',
				'Delaware',
				'Florida',
				'Georgia',
				'Hawaii',
				'Idaho',
				'Illinois',
				'Indiana',
				'Iowa',
				'Kansas',
				'Kentucky',
				'Louisiana',
				'Maine',
				'Maryland',
				'Massachusetts',
				'Michigan',
				'Minnesota',
				'Mississippi',
				'Missouri',
				'Montana',
				'Nebraska',
				'Nevada',
				'New Hampshire',
				'New Jersey',
				'New Mexico',
				'New York',
				'North Carolina',
				'North Dakota',
				'Ohio',
				'Oklahoma',
				'Oregon',
				'Pennsylvania',
				'Rhode Island',
				'South Carolina',
				'South Dakota',
				'Tennessee',
				'Texas',
				'Utah',
				'Vermont',
				'Virginia',
				'Washington',
				'West Virginia',
				'Wisconsin',
				'Wyoming',
			);

			oxygen_vsb_register_condition(
				// Condition Name
				'US State',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => $us_states_list,
					'custom'  => false,
				),
				// Operators
				array( '==', '!=' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_us_state_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Geo'
			);
		}
	}

	// Post Password Protected.
	public static function register_condition_post_password_protected() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Post Password Protected',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_post_password_protected_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Post'
			);
		}
	}

	// At Least 1 Search Result.
	public static function register_condition_at_least_one_search_result() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'At Least 1 Search Result',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_at_least_one_search_result_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Search'
			);
		}
	}

	// CPT has at least 1 published entry.
	public static function register_condition_cpt_has_entry() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			$post_types = get_post_types(
				array(
					'public'   => true,
					'_builtin' => false,
				)
			);
			unset( $post_types['ct_template'] );

			oxygen_vsb_register_condition(
				// Condition Name
				'At least 1 entry exists',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => $post_types,
					'custom'  => false,
				),
				// Operators
				array( 'for this CPT' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_cpt_has_entry_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'CPT'
			);
		}
	}

	// Mobile Detect.
	public static function register_condition_mobile_detect() {
		if ( ! class_exists( 'Mobile_Detect' ) ) {
			require_once 'lib/mobile_detect.php';
		}

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			// $detect = new Mobile_Detect;

			oxygen_vsb_register_condition(
				// Condition Name
				'Mobile',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_is_mobile_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Mobile Detect'
			);

			oxygen_vsb_register_condition(
				// Condition Name
				'Tablet',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_is_tablet_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Mobile Detect'
			);

			oxygen_vsb_register_condition(
				// Condition Name
				'iPhone',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_is_iphone_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Mobile Detect'
			);

			oxygen_vsb_register_condition(
				// Condition Name
				'Android',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_is_android_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Mobile Detect'
			);

			oxygen_vsb_register_condition(
				// Condition Name
				'Handheld',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_is_handheld_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Mobile Detect'
			);
		}
	}

	// Current post Author.
	public static function register_condition_current_cpt_author() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Current Post Author',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_current_cpt_author_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'User'
			);

		}
	}

	// Is Homepage.
	public static function register_condition_is_homepage() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Is Homepage',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_is_homepage_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Other'
			);

		}
	}

	// Author has CPT entry.
	public static function register_condition_author_has_cpt_entry() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			$post_types = get_post_types(
				array(
					'public'   => true,
					'_builtin' => false,
				)
			);
			unset( $post_types['ct_template'] );

			oxygen_vsb_register_condition(
				// Condition Name
				'Author has CPT entry',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => $post_types,
					'custom'  => false,
				),
				// Operators
				array( '==', '!=' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_author_has_cpt_entry_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Author'
			);

		}
	}
	
	// User has at least 1 published item of the selected post type.
	public static function register_condition_user_has_written() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			$post_types = get_post_types(
				array(
					'public' => true,
				)
			);
			unset( $post_types['ct_template'], $post_types['attachment'] );

			oxygen_vsb_register_condition(
				// Condition Name
				'User has written',

				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => $post_types,
					'custom'  => false,
				),

				// Operators
				array( '==', '!=' ),

				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_user_has_written_callback',

				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'User'
			);

		}
	}

	// Is Child of a Specific Page.
	public static function register_condition_is_child_of_specific_page() {
		$args = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$pages = get_posts( $args );

		$post_titles = array();

		foreach ( $pages as $page ) {
			$post_titles[] = $page->post_title;
		}

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Is Child of a Specific Page',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => $post_titles,
					'custom'  => false,
				),
				// Operators
				array( '==', '!=' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_is_child_of_specific_page_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Page'
			);
		}
	}

	// Is Child.
	public static function register_condition_is_child() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Is Child?',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				// Operators
				array( '==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_is_child_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Post'
			);

		}
	}

	// Country.
	public static function register_condition_country() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			
			oxygen_vsb_register_condition(
				// Condition Name
				'Country',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.

				array(
					'options' => self::$countries,
					'custom'  => false,
				),
				
				// Operators
				array( '==', '!=' ),
				
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_country_callback',
				
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Geo'
			);

		}
	}

	// Month.
	public static function register_condition_month() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Month',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
					'custom'  => false,
				),
				// Operators
				array( '==', '!=' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_month_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Other'
			);

		}
	}

	// Referrer URL Parameter.
	public static function register_condition_referrer_url_parameter() {
		add_filter( 'query_vars', array( __CLASS__, 'custom_query_vars_referrer' ) );

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Referrer URL Parameter',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array(),
					'custom'  => true,
				),
				// Operators
				array( '==', '!=' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_referrer_url_parameter_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Other'
			);

		}
	}

	// Archive Type.
	public static function register_condition_archive_type() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Archive Type',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'Posts page (Blog)', 'Category', 'Tag', 'Taxonomy', 'Search', 'Author', 'Date', 'Shop/Product Category/Product Tag' ),
					'custom'  => false,
				),
				// Operators
				array( '==', '!==' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_archive_type_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Archive'
			);

		}
	}

	// MemberPress Membership.
	public static function register_condition_memberpress_membership() {
		$args = array(
			'post_type'      => 'memberpressproduct',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$memberpress_products = get_posts( $args );

		$post_titles = array();

		foreach ( $memberpress_products as $memberpress_product ) {
			$post_titles[] = $memberpress_product->post_title;
		}

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'MemberPress Membership',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => $post_titles,
					'custom'  => false,
				),
				// Operators
				array( '==', '!=' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_memberpress_membership_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'User'
			);
		}
	}

	// RCP Membership.
	public static function register_condition_rcp_membership() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			global $oxy_condition_operators;

			oxygen_vsb_register_condition(
				// Condition Name
				'RCP membership level (enter ID)',
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array(),
					'custom'  => true,
				),
				// Operators
				array( '==', '!=' ),
				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_rcp_membership_callback',
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'User'
			);

		}
	}

	// Language.
	public static function register_condition_language() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Language (visitor)',

				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array_values( self::$languages_array ),
					'custom'  => false,
				),

				// Operators
				array( '==', '!=' ),

				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_language_callback',
				
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Geo'
			);

		}
	}

	// Body Class.
	public static function register_condition_body_class() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name
				'Body Class',

				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => '',
					'custom'  => true,
				),

				// Operators
				array( '==', '!=' ),

				// Callback Function: Name of function that will be used to handle the condition
				'oxy_toolbox_conditions_body_class_callback',

				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Other'
			);

		}
	}

	// Browser Detect.
	public static function register_condition_browser_detect() {
		if ( ! class_exists( 'Browser' ) ) {
			require_once 'lib/Browser.php';
		}

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name.
				'Browser',

				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'Brave', 'Chrome', 'Firefox', 'Edge', 'Internet Explorer', 'Opera', 'Safari', 'Safari on iPhone' ),
					'custom'  => false,
				),

				// Operators.
				array( '==', '!=' ),

				// Callback Function: Name of function that will be used to handle the condition.
				'oxy_toolbox_conditions_browser_detect_callback',

				// Condition Category: Default ones are Archive, Author, Other, Post, User.
				'Other'
			);

		}
	}

	// WooCommerce Conditions.
	public static function register_condition_wc_conditions() {
		// if WooCommerce is not active, abort.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name.
				'Has Empty Cart',

				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( true, false ),
					'custom'  => false,
				),

				// Operators.
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),

				// Callback Function.
				'oxy_toolbox_conditions_wc_empty_cart_callback',

				// Condition Category: Default ones are Archive, Author, Other, Post, User.
				'Woo User'
			);
		}

		// Has Product in Cart.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Has Product in Cart',
				// Values: The array of pre-set values the user can choose from.

				array(
					'custom'  => true,
				),

				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),

				// Callback Function
				'oxy_toolbox_conditions_product_in_cart_callback',

				// Condition Category
				'Woo User'
			);
		}

		// Backorders Allowed.
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Backorders Allowed',

				// Values: The array of pre-set values the user can choose from.
				array(
					'custom'  => false,
					'options' => array( true, false ),
				),

				// Operators
				array( '==' ),
				
				// Callback Function
				'oxy_toolbox_conditions_backorders_allowed_callback',
				
				// Condition Category
				'Woo Product'
			);
		}
		
		// Product Price.
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product Price',

				// Values: The array of pre-set values the user can choose from.
				array(
					'custom'  => true,
				),

				// Operators
				array( '==', '!=', '>=', '<=', 'is_blank', 'is_not_blank' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_price_callback',
				
				// Condition Category
				'Woo Product'
			);
		}
		
		// Product Type.
		$product_types      = wc_get_product_types();
		$product_types_keys = array_keys( $product_types );

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product Type',
				// Values: The array of pre-set values the user can choose from.
				
				array(
					'options' => $product_types_keys,
				),
				
				// Operators
				array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_type_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Product in Category.
		$product_categories = get_terms( array(
			'taxonomy' => 'product_cat',
			'hide_empty' => true,
			'fields' => 'names'
		) );

		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product in Category',
				
				// Values
				array(
					'options' => $product_categories,
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_in_category_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Selected Category has at least One Product.
		$product_categories_all = get_terms( array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
			'fields' => 'names'
		) );

		// Product in Sub Categories of.
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name.
				'Product in Sub Categories of',
	
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => $product_categories_all,
					'custom'  => false,
				),
	
				// Operators.
				array( '==', '!=' ),
	
				// Callback Function: Name of function that will be used to handle the condition.
				'oxy_toolbox_conditions_product_in_sub_categories_of_callback',
	
				// Condition Category: Default ones are Archive, Author, Other, Post, User.
				'Woo Product'
			);
		}
	
		/**
		 * Callback function to handle the condition.
		 *
		 * @param  mixed  $value      Input value - product category selected by the user.
		 * @param  string $operator   Comparison operator, =.
		 *
		 * @return boolean              true or false.
		 */
		function wpdd_product_in_sub_categories_of_callback( $value, $operator ) {
			$selected_product_cat_children_ids_array = get_term_children( get_term_by( 'name', $value, 'product_cat' )->term_id, 'product_cat' );

			$product_in_categories = has_term( $selected_product_cat_children_ids_array, 'product_cat' );
	
			return ( '==' === $operator ) ? $product_in_categories : ! $product_in_categories;
		}

		// Product has Tag.
		$product_tags = get_terms( array(
			'taxonomy' => 'product_tag',
			'hide_empty' => true,
			'fields' => 'names'
		) );
		
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product has Tag',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => $product_tags,
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_in_tag_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Product on Sale.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product on Sale',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( true, false ),
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_on_sale_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Product is Virtual.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product is Virtual',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( true, false ),
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_is_virtual_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Product is Downloadable.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product is Downloadable',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( true, false ),
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_is_downloadable_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Product has Image.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product has Image',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( true, false ),
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_has_image_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Product in Stock.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product in Stock',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( true, false ),
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_product_in_stock_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Has Cart Weight..
		global $oxy_condition_operators;

		$weight_unit = get_option( 'woocommerce_weight_unit' );

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Has Cart Weight (' . $weight_unit . ')',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array(),
					'custom'  => true,
				),
				
				// Operators
				is_array( $oxy_condition_operators ) ? $oxy_condition_operators['int'] : array('==', '!=', '>=', '<=', '>', '<'),
				
				// Callback Function
				'oxy_toolbox_conditions_cart_weight_callback',
				
				// Condition Category
				'Woo User'
			);
		}

		// Is at Endpoint.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Is at Endpoint',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( 'Any', 'Order Pay', 'Order received', 'View order', 'Edit account', 'Edit Addresses', 'Payment methods', 'Lost password', 'Customer Logout' ),
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_endpoint_callback',
				
				// Condition Category
				'Woo User'
			);
		}

		// Product Already Purchased.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Product Already Purchased',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_customer_bought_product_callback',
				
				// Condition Category
				'Woo Product'
			);
		}
		
		// Stock Status.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Stock Status',

				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( 'In stock', 'Out of stock', 'On backorder' ),
					'custom'  => false,
				),

				// Operators
				// array( '==' ),
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_stock_status_callback',
				
				// Condition Category
				'Woo Product'
			);
		}

		// Has Purchased Product.
		global $oxy_condition_operators;

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Has Purchased Product',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'custom'  => true,
				),
				
				// Operators
				is_array( $oxy_condition_operators )  ? $oxy_condition_operators['simple'] : array( '==', '!=' ),
				
				// Callback Function
				'oxy_toolbox_conditions_user_purchased_product_callback',
				
				// Condition Category
				'Woo User'
			);
		}

		// Has Cart Total.
		global $oxy_condition_operators;

		$currency_symbol = get_option( 'woocommerce_currency' );

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Has Cart Total (' . $currency_symbol . ')',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array(),
					'custom'  => true,
				),
				
				// Operators
				is_array( $oxy_condition_operators ) ? $oxy_condition_operators['int'] : array('==', '!=', '>=', '<=', '>', '<'),
				
				// Callback Function
				'oxy_toolbox_conditions_cart_total_callback',
				
				// Condition Category
				'Woo User'
			);
		}

		// Selected Tag has at least One Product.
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Selected Tag has at least One Product',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => $product_tags,
					'custom'  => false,
				),
				
				// Operators
				array( '==' ),
				
				// Callback Function
				'oxy_toolbox_conditions_at_least_one_product_specified_tag_callback',
				
				// Condition Category
				'WooCommerce'
			);
		}

		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'Selected Category has at least One Product',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => $product_categories_all,
					'custom'  => false,
				),
				
				// Operators
				array( '==' ),
				
				// Callback Function
				'oxy_toolbox_conditions_at_least_one_product_specified_cat_callback',
				
				// Condition Category
				'WooCommerce'
			);
		}

		// At least One Featured Product.
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'At least One Featured Product',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				
				// Operators
				array( '==' ),
				
				// Callback Function
				'oxy_toolbox_conditions_at_least_one_featured_product_callback',
				
				// Condition Category
				'WooCommerce'
			);
		}

		// At least One Product on Sale.
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
			oxygen_vsb_register_condition(
				// Condition Name
				'At least One Product on Sale',
				
				// Values: The array of pre-set values the user can choose from.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				
				// Operators
				array( '==' ),
				
				// Callback Function
				'oxy_toolbox_conditions_at_least_one_product_on_sale_callback',
				
				// Condition Category
				'WooCommerce'
			);
		}

	}

	// Is Affiliate (AffiliateWP).
	public static function register_condition_is_affiliate_affiliatewp() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name.
				'Is Affiliate (AffiliateWP)',
				
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				
				// Operators.
				array( '==' ),
				
				// Callback Function: Name of function that will be used to handle the condition.
				'oxy_toolbox_conditions_is_affiliate_affiliatewp_callback',
				
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'User'
			);

		}
	}

	// HTTP Referer.
	public static function register_condition_http_referer() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name.
				'HTTP Referer',
				
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array(),
					'custom'  => true,
				),
				
				// Operators.
				array( 'contains', 'does not contain', 'is exactly' ),
				
				// Callback Function: Name of function that will be used to handle the condition.
				'oxy_toolbox_conditions_http_referer_callback',
				
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Other'
			);

		}
	}
	
	// Is Archive.
	public static function register_condition_is_archive() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name.
				'Is Archive',
				
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				
				// Operators.
				array( '==' ),

				// Callback Function: Name of function that will be used to handle the condition.
				'oxy_toolbox_conditions_is_archive_callback',
				
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Other'
			);

		}
	}
	
	// Is Archive.
	public static function register_condition_post_has_tags() {
		if ( function_exists( 'oxygen_vsb_register_condition' ) ) {

			oxygen_vsb_register_condition(
				// Condition Name.
				'Post Has Tags',
				
				// Values: The array of pre-set values the user can choose from.
				// Set the custom key's value to true to allow users to input custom values.
				array(
					'options' => array( 'true', 'false' ),
					'custom'  => false,
				),
				
				// Operators.
				array( '==' ),

				// Callback Function: Name of function that will be used to handle the condition.
				'oxy_toolbox_conditions_post_has_tags_callback',
				
				// Condition Category: Default ones are Archive, Author, Other, Post, User
				'Post'
			);

		}
	}



	/***************************************************************
	 * Callback functions inside the class
	 ***************************************************************/

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the name of membership level.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function wlm_membership_callback( $value, $operator ) {
		// Get the id of selected level.
		// https://www.php.net/manual/en/function.array-search.php
		$id = array_search( $value, self::$associative_array );

		return $operator == '==' ? wlmapi_is_user_a_member( $id, get_current_user_id() ) : ! wlmapi_is_user_a_member( $id, get_current_user_id() );
	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function post_id_in_array_callback( $value, $operator ) {
		return in_array( get_the_ID(), array_map( 'intval', explode( ',', $value ) ) );
	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the country selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function us_state_callback( $value, $operator ) {
		$url = 'https://ipapi.co/' . self::getRealIpAddr() . '/region/';

		$request = wp_safe_remote_get( $url );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		$region = wp_remote_retrieve_body( $request );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		if ( '==' === $operator ) {
			return strval( $region ) === $value;
		} else {
			return strval( $region ) !== $value;
		}
	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function post_password_protected_callback( $value, $operator ) {
		$current_post = get_post();

		if ( ! $current_post ) {
			return false;
		}

		if ( 'true' === $value ) {
			return post_password_required( $current_post );
		} else {
			return ! post_password_required( $current_post );
		}
	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function at_least_one_search_result_callback( $value, $operator ) {

		global $wp_query;

		if ( 'true' === $value ) {
			return ( $wp_query->found_posts > 0 );
		} else {
			return ( $wp_query->found_posts === 0 );
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the CPT selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function cpt_has_entry_callback( $value, $operator ) {

		return ( wp_count_posts( $value )->publish > 0 );

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function is_mobile_callback( $value, $operator ) {
		$detect = new Mobile_Detect();
		$value  = strtolower( $value ) === 'true' ? true : false;
		return $value ? $detect->isMobile() : ! $detect->isMobile();
	}
	public static function is_tablet_callback( $value, $operator ) {
		$detect = new Mobile_Detect();
		$value  = strtolower( $value ) === 'true' ? true : false;
		return $value ? $detect->isTablet() : ! $detect->isTablet();
	}
	public static function is_iphone_callback( $value, $operator ) {
		$detect = new Mobile_Detect();
		$value  = strtolower( $value ) === 'true' ? true : false;
		return $value ? $detect->isiPhone() : ! $detect->isiPhone();
	}
	public static function is_android_callback( $value, $operator ) {
		$detect = new Mobile_Detect();
		$value  = strtolower( $value ) === 'true' ? true : false;
		return $value ? $detect->isAndroidOS() : ! $detect->isAndroidOS();
	}
	public static function is_handheld_callback( $value, $operator ) {
		$detect = new Mobile_Detect();

		$value = strtolower( $value ) === 'true' ? true : false;

		$isMobileDevice = $detect->isMobile() || $detect->isiPhone() || $detect->isiPad() || $detect->isAndroidOS() || $detect->isBlackBerry() || $detect->iswebOS() || $detect->isSymbianOS() || $detect->isWindowsMobileOS() || $detect->isWindowsPhoneOS() || $detect->isMotorola() || $detect->isSamsung() || $detect->isSamsungTablet() || $detect->isSonyTablet() || $detect->isNintendo() || $detect->isHTC() || $detect->isNexus() || $detect->isOnePlus() || $detect->isGoogleTablet() || $detect->isKindle();

		return $value ? $isMobileDevice : ! $isMobileDevice;

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function current_cpt_author_callback( $value, $operator ) {

		// if we are not on a singular page or
		// if user is not logged in
		// return false.
		if ( ! is_singular() || ! is_user_logged_in() ) {
			return false;
		}

		$current_post = get_post();

		if ( ! $current_post ) {
			return false;
		}

		if ( 'true' === $value ) {
			return ( get_the_author_meta( 'ID', $current_post->post_author ) === wp_get_current_user()->ID );
		} else {
			return ( get_the_author_meta( 'ID', $current_post->post_author ) !== wp_get_current_user()->ID );
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function is_homepage_callback( $value, $operator ) {

		if ( 'true' === $value ) {
			return ( is_front_page() );
		} else {
			return ( ! is_front_page() );
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the post type selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function author_has_cpt_entry_callback( $value, $operator ) {
		// if the current user is not logged in or is not an author, bail out.
		if ( ! is_user_logged_in() || self::check_user_role( array( 'author' ) ) ) {
			return false;
		}

		$posts_count = intval( count_user_posts( get_current_user_id(), $value ) );
	
		return ( '==' === $operator ) ? $posts_count > 0 : $posts_count === 0;
	}
	
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the post type selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function user_has_written_callback( $value, $operator ) {
		if ( 'true' === get_option( self::$prefix . self::$mod . 'user_has_written', false ) ) {
			$posts_count = intval( count_user_posts( get_current_user_id(), $value, true ) );
		} else {
			$posts_count = intval( count_user_posts( get_current_user_id(), $value ) );
		}
	
		return ( '==' === $operator ) ? $posts_count > 0 : $posts_count === 0;
	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the month selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function is_child_of_specific_page_callback( $value, $operator ) {

		// if we are not on a static Page AND if the current Page is not a child page, bail out.
		if ( ! is_page() && 0 === wp_get_post_parent_id( get_the_ID() ) ) {
			return false;
		}

		// Selected Page.
		$selected_page_object = get_page_by_title( $value, OBJECT, 'page' );
		$id                   = $selected_page_object->ID;

		if ( '==' === $operator ) {
			return ( wp_get_post_parent_id( get_the_ID() ) === $id );
		} else {
			return ( wp_get_post_parent_id( get_the_ID() ) !== $id );
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the month selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function is_child_callback( $value, $operator ) {

		if ( 'true' === $value ) {
			return ( 0 !== wp_get_post_parent_id( get_the_ID() ) );
		} else {
			return ( 0 === wp_get_post_parent_id( get_the_ID() ) );
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the country selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function country_callback( $value, $operator ) {

		$geolocation = 'https://api.iplocation.net/?ip=' . self::getRealIpAddr();

		$request = wp_safe_remote_get( $geolocation );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		if ( '==' === $operator ) {
			return ( strval( $response->country_code2 ) === array_search( $value, self::$countries ) );
		} else {
			return ( strval( $response->country_code2 ) !== array_search( $value, self::$countries ) );
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the month selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function month_callback( $value, $operator ) {

		if ( '==' === $operator ) {
			return ( date( 'F' ) === $value );
		} else {
			return ( date( 'F' ) !== $value );
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the text entered by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function referrer_url_parameter_callback( $value, $operator ) {

		$referrer = sanitize_text_field( get_query_var( 'referrer' ) );

		if ( '==' === $operator ) {
			return $referrer === $value;
		} else {
			return $referrer !== $value;
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, type of archive page.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function archive_type_callback( $value, $operator ) {

		if ( $operator == '==' ) {
			switch ( $value ) {
				case 'Posts page (Blog)':
					return ( is_home() ) ? true : false;
					break;

				case 'Category':
					return ( is_category() ) ? true : false;
					break;

				case 'Tag':
					return ( is_tag() ) ? true : false;
					break;

				case 'Taxonomy':
					return ( is_tax() ) ? true : false;
					break;

				case 'Search':
					return ( is_search() ) ? true : false;
					break;

				case 'Author':
					return ( is_author() ) ? true : false;
					break;

				case 'Date':
					return ( is_date() ) ? true : false;
					break;

				case 'Shop/Product Category/Product Tag':
					return ( is_shop() || is_product_taxonomy() ) ? true : false;
					break;

				default:
					return true;
					break;
			}
		} else {
			switch ( $value ) {
				case 'Posts page (Blog)':
					return ( is_home() ) ? false : true;
					break;

				case 'Category':
					return ( is_category() ) ? false : true;
					break;

				case 'Tag':
					return ( is_tag() ) ? false : true;
					break;

				case 'Taxonomy':
					return ( is_tax() ) ? false : true;
					break;

				case 'Search':
					return ( is_search() ) ? false : true;
					break;

				case 'Author':
					return ( is_author() ) ? false : true;
					break;

				case 'Date':
					return ( is_date() ) ? false : true;
					break;

				case 'Shop/Product Category/Product Tag':
					return ( is_shop() || is_product_taxonomy() ) ? false : true;
					break;

				default:
					return true;
					break;
			}
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the name of membership.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function memberpress_membership_callback( $value, $operator ) {

		$memberpressproduct_object = get_page_by_title( $value, OBJECT, 'memberpressproduct' );

		$id = $memberpressproduct_object->ID;

		if ( $operator == '==' ) {
			// return ( current_user_can( 'memberpress_product_authorized_' . $id . '' ) ) ? true : false;
			return ( current_user_can( 'mepr-active', 'membership:' . $id . '' ) ) ? true : false;
		} else {
			// return ( current_user_can( 'memberpress_product_authorized_' . $id . '' ) ) ? false : true;
			return ( current_user_can( 'mepr-active', 'membership:' . $id . '' ) ) ? false : true;
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, ID of RCP subscription level.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function rcp_membership_callback( $value, $operator ) {

		if ( ! function_exists( 'rcp_get_customer_membership_level_ids' ) ) {
			return false;
		}

		if ( '==' === $operator ) {
			return ( in_array( intval( $value ), rcp_get_customer_membership_level_ids() ) ) ? true : false;
		} else {
			return ( in_array( intval( $value ), rcp_get_customer_membership_level_ids() ) ) ? false : true;
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the language selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function language_callback( $value, $operator ) {

		// Get the languages set in the browser.
		$languages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

		// Get the position of "," in languages.
		$pos = strpos( $languages, ',' );

		// Get the first language before the first occurence of comma.
		$language = substr( $languages, 0, $pos );

		if ( '==' === $operator ) {
			return $language === array_search( $value, self::$languages_array );
		} else {
			return $language !== array_search( $value, self::$languages_array );
		}

	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function body_class_callback( $value, $operator ) {

		if ( '==' === $operator ) {
			return in_array( $value, get_body_class() );
		} else {
			return ! in_array( $value, get_body_class() );
		}

	}

	/**
	 * Callback function to handle the Browser condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function browser_detect_callback( $value, $operator ) {
		$browser = new Browser();

		if ( '==' === $operator ) {
			switch ( $value ) {
				case 'Brave':
					return Browser::BROWSER_BRAVE === $browser->getBrowser();
					break;

				case 'Chrome':
					return Browser::BROWSER_CHROME === $browser->getBrowser();
					break;

				case 'Firefox':
					return Browser::BROWSER_FIREFOX === $browser->getBrowser();
					break;

				case 'Edge':
					return Browser::BROWSER_EDGE === $browser->getBrowser();
					break;

				case 'Internet Explorer':
					return Browser::BROWSER_IE === $browser->getBrowser();
					break;

				case 'Opera':
					return Browser::BROWSER_OPERA === $browser->getBrowser();
					break;

				case 'Safari':
					return Browser::BROWSER_SAFARI === $browser->getBrowser();
					break;

				case 'Safari on iPhone':
					return Browser::BROWSER_IPHONE === $browser->getBrowser();
					break;

				default:
					return true;
					break;
			}
		} else {
			switch ( $value ) {
				case 'Brave':
					return Browser::BROWSER_BRAVE !== $browser->getBrowser();
					break;

				case 'Chrome':
					return Browser::BROWSER_CHROME !== $browser->getBrowser();
					break;

				case 'Firefox':
					return Browser::BROWSER_FIREFOX !== $browser->getBrowser();
					break;

				case 'Edge':
					return Browser::BROWSER_EDGE !== $browser->getBrowser();
					break;

				case 'Internet Explorer':
					return Browser::BROWSER_IE !== $browser->getBrowser();
					break;

				case 'Opera':
					return Browser::BROWSER_OPERA !== $browser->getBrowser();
					break;

				case 'Safari':
					return Browser::BROWSER_SAFARI !== $browser->getBrowser();
					break;

				case 'Safari on iPhone':
					return Browser::BROWSER_IPHONE !== $browser->getBrowser();
					break;

				default:
					return true;
					break;
			}
		}
	}

	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function wc_empty_cart_callback( $value, $operator ) {

		$emptycart = WC()->cart->is_empty();

		$value = (bool) $value;

		return oxy_condition_eval_string( $emptycart, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the name of the product.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_in_cart_callback( $value, $operator ) {

		//$product_object = get_page_by_title( $value, OBJECT, 'product' );
		$product_object = get_post( intval($value));
		if ( is_null( $product_object ) ) {
			return false;
		}

		$id = $product_object->ID;

		$product_cart_id = WC()->cart->generate_cart_id( $id );

		if ( '==' === $operator ) {
			return WC()->cart->find_product_in_cart( $product_cart_id );
		} elseif ( '!=' === $operator ) {
			return ! WC()->cart->find_product_in_cart( $product_cart_id );
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the name of product type.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_type_callback( $value, $operator ) {

		// Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages.
		if ( wc_get_product() && ! is_shop() && ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$product_id = $product->get_id();

		$product_type = WC_Product_Factory::get_product_type( $product_id );

		$value = (string) $value;

		if ( '==' === $operator ) {
			return ( $product_type === $value ) ? true : false;
		} elseif ( '!=' === $operator ) {
			return ( $product_type !== $value ) ? false : true;
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the name of product type.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function backorders_allowed_callback( $value, $operator ) {

		if ( wc_get_product() ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$backorders_allowed = $product->backorders_allowed();

		$value = (bool) $value;

		return oxy_condition_eval_string( $backorders_allowed, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the name of product type.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_price_callback( $value, $operator ) {

		if ( wc_get_product() ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$product_price = $product->get_price();

		if ( '==' === $operator ) {
			return $product_price === $value;
		} elseif ( '!=' === $operator ) {
			return $product_price !== $value;
		} elseif ( '>=' === $operator ) {
			return $product_price >= $value;
		} elseif ( '<=' === $operator ) {
			return $product_price <= $value;
		} elseif ( 'is_not_blank' === $operator ) {
			return $product_price;
		} elseif ( 'is_blank' === $operator ) {
			return ! $product_price;
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - Product Category
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_in_category_callback( $value, $operator ) {

		$product_in_category = has_term( $value, 'product_cat' );

		return ( '==' === $operator ) ? $product_in_category : ! $product_in_category;

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - Product Category
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_in_sub_categories_of_callback( $value, $operator ) {

		$selected_product_cat_children_ids_array = get_term_children( get_term_by( 'name', $value, 'product_cat' )->term_id, 'product_cat' );

		$product_in_categories = has_term( $selected_product_cat_children_ids_array, 'product_cat' );

		return ( '==' === $operator ) ? $product_in_categories : ! $product_in_categories;

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the name of product tag.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_in_tag_callback( $value, $operator ) {

		$product_has_tag = has_term( $value, 'product_tag' );

		if ( '==' === $operator ) {
			return $product_has_tag;
		} elseif ( '!=' === $operator ) {
			return ! $product_has_tag;
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_on_sale_callback( $value, $operator ) {

		// TO DO include variable products

		// Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
		if ( wc_get_product() && ! is_shop() && ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$product_on_onsale = $product->is_on_sale();

		$value = (bool) $value;

		return oxy_condition_eval_string( $product_on_onsale, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_is_virtual_callback( $value, $operator ) {

		// TO DO include variable products

		// Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
		if ( wc_get_product() && ! is_shop() && ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$product_is_virtual = $product->is_virtual();

		$value = (bool) $value;

		return oxy_condition_eval_string( $product_is_virtual, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_is_downloadable_callback( $value, $operator ) {

		// Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
		if ( wc_get_product() && ! is_shop() && ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$productisdownloadable = $product->is_downloadable();

		$value = (bool) $value;

		return oxy_condition_eval_string( $productisdownloadable, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_has_image_callback( $value, $operator ) {

		// Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
		if ( wc_get_product() && ! is_shop() && ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$product_image = $product->get_image_id();

		$value = (bool) $value;

		if ( $product_image != null ) {
			$product_has_image = true;
		} else {
			$product_has_image = false;
		}

		return oxy_condition_eval_string( $product_has_image, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function product_in_stock_callback( $value, $operator ) {

		// Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
		if ( wc_get_product() && ! is_shop() && ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$product_in_stock = $product->is_in_stock();

		$value = (bool) $value;

		return oxy_condition_eval_string( $product_in_stock, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the cart weight value.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function cart_weight_callback( $value, $operator ) {

		$cart_weight = WC()->cart->cart_contents_weight;

		$cart_weight = intval( $cart_weight );

		$value = intval( $value );

		return oxy_condition_eval_int( $cart_weight, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - The name of endpoint.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	function endpoint_callback( $value, $operator ) {

		switch ( $value ) {
			case 'Any':
				$endpoint_url = is_wc_endpoint_url();
				break;

			case 'Order Pay':
				$endpoint_url = is_wc_endpoint_url( 'order-pay' );
				break;

			case 'Order received':
				$endpoint_url = is_wc_endpoint_url( 'order-received' );
				break;

			case 'View order':
				$endpoint_url = is_wc_endpoint_url( 'view-order' );
				break;

			case 'Edit account':
				$endpoint_url = is_wc_endpoint_url( 'edit-account' );
				break;

			case 'Edit Addresses':
				$endpoint_url = is_wc_endpoint_url( 'edit-address' );
				break;

			case 'Payment methods':
				$endpoint_url = is_wc_endpoint_url( 'add-payment-method' );
				break;

			case 'Lost password':
				$endpoint_url = is_wc_endpoint_url( 'lost-password' );
				break;

			case 'Customer Logout':
				$endpoint_url = is_wc_endpoint_url( 'customer-logout' );
				break;
		}

		if ( '==' === $operator ) {
			return $endpoint_url;
		} elseif ( '!=' === $operator ) {
			return ! $endpoint_url;
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function customer_bought_product_callback( $value, $operator ) {

		// Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
		if ( wc_get_product() && ! is_shop() && ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$product_id                  = $product->get_id();
		$current_user                = wp_get_current_user();
		$customer_has_bought_product = wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product_id );

		$should_have_bought = false;

		if ( $value == 'true' ) {
			$should_have_bought = true;
		}

		if ( $operator == '!=' ) {
			return ( $customer_has_bought_product !== $should_have_bought );
		} else {
			return ( $customer_has_bought_product === $should_have_bought );
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the stock status selected by the user
	 * @param  string $operator   ==.
	 *
	 * @return boolean              true or false.
	 */
	public static function stock_status_callback( $value, $operator ) {

		if ( wc_get_product() ) {
			$product = wc_get_product();
		} else {
			return false;
		}

		$stock_status = $product->get_stock_status();

		if ( '==' === $operator ) {
			if ( 'In stock' === $value ) {
				return 'instock' === $stock_status;
			} elseif ( 'Out of stock' === $value ) {
				return 'outofstock' === $stock_status;
			} elseif ( 'On backorder' === $value ) {
				return 'onbackorder' === $stock_status;
			}
		} else {
			if ( 'In stock' === $value ) {
				return 'instock' !== $stock_status;
			} elseif ( 'Out of stock' === $value ) {
				return 'outofstock' !== $stock_status;
			} elseif ( 'On backorder' === $value ) {
				return 'onbackorder' !== $stock_status;
			}
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the product title.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function user_purchased_product_callback( $value, $operator ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		//$product_object = get_page_by_title( $value, OBJECT, 'product' );
		$product_object = get_post( intval($value));

		if ( is_null( $product_object ) ) {
			return false;
		}

		$id = $product_object->ID;

		$current_user = wp_get_current_user();

		$customer_bought_product = wc_customer_bought_product( $current_user->user_email, $current_user->ID, $id );

		if ( '==' === $operator ) {
			return $customer_bought_product;
		} elseif ( '!=' === $operator ) {
			return ! $customer_bought_product;
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, the cart total.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function cart_total_callback( $value, $operator ) {

		$cart_total = WC()->cart->get_cart_contents_total();

		$cart_total = intval( $cart_total );

		$value = intval( $value );

		return oxy_condition_eval_int( $cart_total, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, name of the selected tag.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function at_least_one_product_specified_tag_callback( $value, $operator ) {

		$args = array(
			'status'    => 'publish',
			'limit'     => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'product_tag',
					'field'    => 'name',
					'terms'    => array( $value ),
				),
			),
		);

		// Create the new query.
		$query = new WC_Product_Query( $args );

		$products = $query->get_products();

		return count( $products ) > 0;

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, name of the selected category.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function at_least_one_product_specified_cat_callback( $value, $operator ) {

		$args = array(
			'status'    => 'publish',
			'limit'     => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'name',
					'terms'    => array( $value ),
				),
			),
		);

		// Create the new query.
		$query = new WC_Product_Query( $args );

		$products = $query->get_products();

		return count( $products ) > 0;

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function at_least_one_featured_product_callback( $value, $operator ) {

		$args = array(
			'status'    => 'publish',
			'limit'     => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				),
			),
		);

		// Create the new query.
		$query = new WC_Product_Query( $args );

		$products = $query->get_products();

		$value = (bool) $value;

		return $value ? count( $products ) > 0 : count( $products ) === 0;

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function at_least_one_product_on_sale_callback( $value, $operator ) {

		$args = array(
			'post_type'      => array( 'product' ),
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_sale_price',
					'value'   => '',
					'compare' => '!=',
				),
			),
		);

		// Create the new query.
		$query = new WP_Query( $args );

		$value = (bool) $value;

		return $value ? $query->post_count > 0 : $query->post_count === 0;

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function is_affiliate_affiliatewp_callback( $value, $operator ) {

		if ( ! class_exists( 'Affiliate_WP' ) ) {
			return false;
		}

		if ( 'true' === $value ) {
			return ( affwp_is_affiliate() && affwp_is_active_affiliate() );
		} else {
			return ( ! ( affwp_is_affiliate() && affwp_is_active_affiliate() ) );
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function http_referer_callback( $value, $operator ) {

		// if ( ! wp_get_raw_referer() ) {
		// 	return false;
		// }

		if ( 'contains' === $operator ) {
			return strpos( wp_get_raw_referer(), $value );
		} elseif ( 'does not contain' === $operator ) {
			return ! strpos( wp_get_raw_referer(), $value );
		} else {
			return wp_get_raw_referer() === $value;
		}

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function is_archive_callback( $value, $operator ) {

		return 'true' === $value ? is_archive() : ! is_archive();

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator selected by the user.
	 *
	 * @return boolean              true or false.
	 */
	public static function post_has_tags_callback( $value, $operator ) {

		return 'true' === $value ? has_tag() : ! has_tag();

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator, =.
	 *
	 * @return boolean              true or false.
	 */
	public static function published_during_last_callback( $value, $operator ) {

		$args = array(
			'date_query' => array(
				array(
					'after' => $value . ' ago',
				),
			),
	
			'posts_per_page' => -1,
		);
	
		$query = new WP_Query( $args );
	
		return in_array( get_the_ID(), wp_list_pluck( $query->posts, 'ID' ) );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, value selected by the user.
	 * @param  string $operator   Comparison operator, =.
	 *
	 * @return boolean              true or false.
	 */
	public static function polylang_callback( $value, $operator ) {

		$my_lang = pll_current_language();
		
		global $OxygenConditions;
		
		return $OxygenConditions->eval_string( $my_lang, $value, $operator );

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - in this case, true or false selected by the user.
	 * @param  string $operator   Comparison operator, =.
	 *
	 * @return boolean              true or false.
	 */
	public static function is_blog_callback( $value, $operator ) {

		return ( 'true' === $value ) ? is_home() : ! is_home();

	}
	/**
	 * Callback function to handle the condition.
	 *
	 * @param  mixed  $value      Input value - value set by the user.
	 * @param  string $operator   Comparison operator, =.
	 *
	 * @return boolean              true or false.
	 */
	public static function date_time_callback( $value, $operator ) {

		// Create a new DateTime object using the user-supplied date string in the timezone set in WP settings.
		$date_input_object = new DateTimeImmutable( $value, wp_timezone() );

		// Format it into a Unix timestamp.
		$date_input_timestamp = $date_input_object->format( 'U' );
		
		// $current_time_timestamp = current_datetime()->format( 'U' );

		if ( 'is after' === $operator ) {
			return ( time() > $date_input_timestamp );
		} elseif ( 'is before' === $operator ) {
			return ( time() < $date_input_timestamp );
		} else {
			return ( time() === $date_input_timestamp );
		}

	}

	/***************************************************************
	 * Mod options
	 ***************************************************************/

	public static function mod_register_options() {
		foreach ( self::$option_names as $option_name ) {
			add_option( self::$prefix . self::$mod . $option_name, false );
			register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . $option_name, array( __CLASS__, 'sanitize_module_option' ) );
		}
	}

	public static function sanitize_module_option( $show ) {
		if ( $show === 'true' ) {
			return 'true';
		}

		return '';
	}

	public static function mod_form_options() { ?>
		<button type="button" class="collapsible"></button>
		<div class="module-settings">
			<div class="module-settings-wrap">
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>archive_type" name="<?php echo self::$prefix . self::$mod; ?>archive_type" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'archive_type' ), 'true' ); ?> />
					<?php
					_e( 'Archive Type' );
					echo '<span class="archive condition-label">Archive</span>';
					?>
					<?php _e( '<p>Registers a condition to control the output of elements on archive pages based on the archive type.<br/><em>For use in the Template that applies to more than one type of archive.</em></p>' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>at_least_one_search_result" name="<?php echo self::$prefix . self::$mod; ?>at_least_one_search_result" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'at_least_one_search_result' ), 'true' ); ?> />
					<?php
					_e( 'At Least 1 Search Result' );
					echo '<span class="search condition-label">Search</span>';
					?>
					<?php _e( '<p>Enables you to conditionally output elements depending on whether there is at least one search result or not.<br/><em>For use in the Template that applies to search results.</em></p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>author_has_cpt_entry" name="<?php echo self::$prefix . self::$mod; ?>author_has_cpt_entry" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'author_has_cpt_entry' ), 'true' ); ?> />
					<?php
					_e( 'Author has CPT entry' );
					echo '<span class="author condition-label">Author</span>';
					?>
					<?php _e( '<p>Registers a custom condition using which elements can be output only if the current user is logged in, is an author and has at least 1 published entry of the Custom Post Type to be set in the condition.</p>' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>body_class" name="<?php echo self::$prefix . self::$mod; ?>body_class" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'body_class' ), 'true' ); ?> />
					<?php
					_e( 'Body Class' );
					echo '<span class="other condition-label">Other</span>';
					?>
					<?php _e( '<p>Registers a custom condition using which elements can be output based on whether the page has (or not has) the entered body class.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>browser_detect" name="<?php echo self::$prefix . self::$mod; ?>browser_detect" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'browser_detect' ), 'true' ); ?> />
					<?php
					_e( 'Browser Detect' );
					echo '<span class="other condition-label">Other</span>';
					?>
					<?php _e( "<p>Registers a custom condition using which elements can be output conditionally depending on the visitor's browser.</p>" ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>country" name="<?php echo self::$prefix . self::$mod; ?>country" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'country' ), 'true' ); ?> />
					<?php
					_e( 'Country' );
					echo '<span class="geo condition-label">Geo</span>';
					?>
					<?php _e( '<p>Registers a custom condition for geo-targeting your visitors based on their country.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>cpt_has_entry" name="<?php echo self::$prefix . self::$mod; ?>cpt_has_entry" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'cpt_has_entry' ), 'true' ); ?> />
					<?php
					_e( 'CPT Has At Least 1 Published Entry' );
					echo '<span class="cpt condition-label">CPT</span>';
					?>
					<?php _e( '<p>Enables you to conditionally output elements only if the specified Custom Post Type has at least one published entry.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>current_cpt_author" name="<?php echo self::$prefix . self::$mod; ?>current_cpt_author" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'current_cpt_author' ), 'true' ); ?> />
					<?php
					_e( 'Current Post Author' );
					echo '<span class="user condition-label">User</span>';
					?>
					<?php _e( '<p>Enables you to conditionally output elements on singular pages of any post type if the page being viewed is published by the author of that entry.<br/><em>For use in the Template that applies to singular entries of any post type.</em></p>' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>date_time" name="<?php echo self::$prefix . self::$mod; ?>date_time" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'date_time' ), 'true' ); ?> />
					<?php
					_e( 'Date and time' );
					echo '<span class="other condition-label">Other</span>';
					?>
					<?php _e( '<p>Enables you to conditionally output elements before, after or at a specified date or date and time.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>http_referer" name="<?php echo self::$prefix . self::$mod; ?>http_referer" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'http_referer' ), 'true' ); ?> />
					<?php
					_e( 'HTTP Referer' );
					echo '<span class="other condition-label">Other</span>';
					?>
					<?php _e( '<p>Enables you to conditionally output elements depending on whether the provided string is present in or not present in or is exactly equal to the referring page URL.</em></p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>is_archive" name="<?php echo self::$prefix . self::$mod; ?>is_archive" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'is_archive' ), 'true' ); ?> />
					<?php
					_e( 'Is Archive' );
					echo '<span class="other condition-label">Other</span>';
					?>
					<?php _e( '<p>Is the current page an archive or not?</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>is_blog" name="<?php echo self::$prefix . self::$mod; ?>is_blog" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'is_blog' ), 'true' ); ?> />
					<?php
					_e( 'Is Blog' );
					echo '<span class="other condition-label">Other</span>';
					?>
					<?php _e( '<p>Is the current page the Blog Posts Index i.e., Posts page?</p>' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>is_affiliate_affiliatewp" name="<?php echo self::$prefix . self::$mod; ?>is_affiliate_affiliatewp" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'is_affiliate_affiliatewp' ), 'true' ); ?> />
					<?php
					_e( 'Is Affiliate (AffiliateWP)' );
					echo '<span class="user condition-label">User</span>';
					?>
					<?php _e( '<p>Is the current logged in user an affiliate?<br/><em>Needs AffiliateWP plugin.</em></p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>is_child" name="<?php echo self::$prefix . self::$mod; ?>is_child" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'is_child' ), 'true' ); ?> />
					<?php
					_e( 'Is Child' );
					echo '<span class="post condition-label">Post</span>';
					?>
					<?php _e( '<p>Registers a custom condition using which elements can be output on the front end only if the current single page being viewed is a child i.e., has a parent.<br/><em>Works on single views of any post type.</em></p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>is_child_of_specific_page" name="<?php echo self::$prefix . self::$mod; ?>is_child_of_specific_page" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'is_child_of_specific_page' ), 'true' ); ?> />
					<?php
					_e( 'Is Child of a Specific Page' );
					echo '<span class="page condition-label">Page</span>';
					?>
					<?php _e( '<p>Registers a custom condition using which elements can be output only if the current Page is a child of the selected Page.<br/><em>For use when editing a static Page or a Template that applies all the Pages.</em></p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>is_homepage" name="<?php echo self::$prefix . self::$mod; ?>is_homepage" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'is_homepage' ), 'true' ); ?> />
					<?php
					_e( 'Is Homepage' );
					echo '<span class="page condition-label">Page</span>';
					?>
					<?php _e( '<p>Enables you to conditionally output elements depending on whether the current page is the homepage or an inner page.<br/><em>For use in the Template that applies to all Pages.</em></p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>language" name="<?php echo self::$prefix . self::$mod; ?>language" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'language' ), 'true' ); ?> />
					<?php
					_e( "Language (visitor)" );
					echo '<span class="geo condition-label">Geo</span>';
					?>
					<?php _e( "<p>Registers a condition using which elements can be conditionally output based on the visitor's language.</p>" ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>memberpress_membership" name="<?php echo self::$prefix . self::$mod; ?>memberpress_membership" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'memberpress_membership' ), 'true' ); ?> />
					<?php
					_e( 'MemberPress Membership' );
					echo '<span class="user condition-label">User</span>';
					?>
					<?php _e( '<p>Register a custom condition using which elements can be output conditionally based on whether the user has purchased/subscribed to a membership using MemberPress.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>mobile_detect" name="<?php echo self::$prefix . self::$mod; ?>mobile_detect" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'mobile_detect' ), 'true' ); ?> />
					<?php
					_e( 'Mobile Detect' );
					echo '<span class="mobile condition-label">Mobile</span>';
					?>
					<?php _e( '<p>Enables you to conditionally output elements for various devices like mobiles, tablets, iOS, Android.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>month" name="<?php echo self::$prefix . self::$mod; ?>month" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'month' ), 'true' ); ?> />
					<?php
					_e( 'Month' );
					echo '<span class="other condition-label">Other</span>';
					?>
					<?php _e( '<p>Registers a condition using which elements can be set to be output (or not be output) during specific month(s).</p>' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>polylang" name="<?php echo self::$prefix . self::$mod; ?>polylang" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'polylang' ), 'true' ); ?> />
					<?php
					_e( 'Polylang Locale' );
					echo '<span class="post condition-label">Post</span>';
					?>
					<?php _e( '<p>Registers a condition using which elements can be set to be output based on the post (of any post type) language set using Polylang.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>post_has_tags" name="<?php echo self::$prefix . self::$mod; ?>post_has_tags" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'post_has_tags' ), 'true' ); ?> />
					<?php
					_e( 'Post Has Tags' );
					echo '<span class="post condition-label">Post</span>';
					?>
					<?php _e( '<p>Enables you to output elements on single posts (of the default `post` post type) only if the current post has at least one tag set.</p>' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>post_id_in_array" name="<?php echo self::$prefix . self::$mod; ?>post_id_in_array" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'post_id_in_array' ), 'true' ); ?> />
					<?php
					_e( 'Post ID in Array' );
					echo '<span class="post condition-label">Post</span>';
					?>
					<?php _e( '<p>Enables you to output elements on single posts (of any post type) only if the current post\'s ID is in the array of supplied IDs.<br/><em>For use in the Template that applies to a singular post of any post type.</em></p>' ); ?>
				</div>
		
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>post_password_protected" name="<?php echo self::$prefix . self::$mod; ?>post_password_protected" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'post_password_protected' ), 'true' ); ?> />
					<?php
					_e( 'Post Password Protected' );
					echo '<span class="post condition-label">Post</span>';
					?>
					<?php _e( '<p>Enables you to password protect the entire content of single pages (Pages or Posts or CPTs) including the content coming from the Oxygen editor.<br/><em>Typically used in the Template that applies to a singular post of any post type.</em></p>' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>published_during_last" name="<?php echo self::$prefix . self::$mod; ?>published_during_last" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'published_during_last' ), 'true' ); ?> />
					<?php
					_e( 'Post Published During the Last' );
					echo '<span class="post condition-label">Post</span>';
					?>
					<?php _e( '<p>Enables you to output a post only if it has been published in the last 1 week/month/year.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>rcp_membership" name="<?php echo self::$prefix . self::$mod; ?>rcp_membership" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'rcp_membership' ), 'true' ); ?> />
					<?php
					_e( 'RCP Membership Level' );
					echo '<span class="user condition-label">User</span>';
					?>
					<?php _e( '<p>Registers a custom condition using which elements can be ouput depending on whether the current logged in user is an active member of a specified membership level when using Restrict Content Pro or not.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>referrer_url_parameter" name="<?php echo self::$prefix . self::$mod; ?>referrer_url_parameter" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'referrer_url_parameter' ), 'true' ); ?> />
					<?php
					_e( 'Referrer URL Parameter' );
					echo '<span class="other condition-label">Other</span>';
					?>
					<?php _e( '<p>Enables you to output elements based on the value of "referrer" URL parameter like /?referrer=facebook.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>us_state" name="<?php echo self::$prefix . self::$mod; ?>us_state" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'us_state' ), 'true' ); ?> />
					<?php
					_e( 'US State' );
					echo '<span class="geo condition-label">Geo</span>';
					?>
					<?php _e( '<p>Enables you to output elements to users visiting from a specific state in the US.</p>' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>user_has_written" name="<?php echo self::$prefix . self::$mod; ?>user_has_written" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'user_has_written' ), 'true' ); ?> />
					<?php
					_e( 'User has written' );
					echo '<span class="user condition-label">User</span>';
					?>
					<?php _e( '<p>Enables you to output elements depending on whether or not the current user viewing the page has written at least 1 item of the selected public post type.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>wc_conditions" name="<?php echo self::$prefix . self::$mod; ?>wc_conditions" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'wc_conditions' ), 'true' ); ?> />
					<?php
					_e( 'WooCommerce Conditions' );
					echo '<span class="woocommerce condition-label">WooCommerce</span>';
					?>
					<?php _e( '<p>Adds Woocommerce-focused conditions put together by <a href="https://wordpress.org/plugins/wplit-woo-conditions-for-oxygen/" target="_blank">David Browne</a> to help you build more dynamic e-commerce sites.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>wlm_memberships" name="<?php echo self::$prefix . self::$mod; ?>wlm_memberships" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'wlm_memberships' ), 'true' ); ?> />
					<?php
					_e( 'WLM membership(s)' );
					echo '<span class="user condition-label">User</span>';
					?>
					<?php _e( '<p>Enables you to output elements only if the currently logged in user belongs to the selected membership level when using WishList Member.</p>' ); ?>
				</div>			
			</div>
		</div>
			
		<?php
	}

	/***************************************************************
	 * Helper methods
	 ***************************************************************/

	/**
	 * Function to return user's IP address.
	 *
	 * @link https://stackoverflow.com/a/12553240
	 */
	public static function getRealIpAddr() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) { // check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	public static function check_user_role( $roles, $user_id = null ) {
		if ( $user_id ) {
			$user = get_userdata( $user_id );
		} else {
			$user = wp_get_current_user();
		}
		if ( empty( $user ) ) {
			return false;
		}
		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $roles ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Add custom query variable called `referrer`.
	 *
	 * @param  array $vars Existing query variables whitelist
	 *
	 * @return array       Modified query variables whitelist
	 */
	public static function custom_query_vars_referrer( $vars ) {
		$vars[] = 'referrer';

		return $vars;
	}
}

/***************************************************************
 * Callback functions outside the class
 ***************************************************************/

// Callbacks cannot be a member method of a class because in Oxygen's conditions API, the presence of callback is checked using function_exists, rather than method_exists.

function oxy_toolbox_conditions_post_id_in_array_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::post_id_in_array_callback( $value, $operator );
}

function oxy_toolbox_conditions_wlm_membership_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::wlm_membership_callback( $value, $operator );
}

function oxy_toolbox_conditions_us_state_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::us_state_callback( $value, $operator );
}

function oxy_toolbox_conditions_post_password_protected_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::post_password_protected_callback( $value, $operator );
}

function oxy_toolbox_conditions_at_least_one_search_result_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::at_least_one_search_result_callback( $value, $operator );
}

function oxy_toolbox_conditions_cpt_has_entry_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::cpt_has_entry_callback( $value, $operator );
}

function oxy_toolbox_conditions_is_mobile_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_mobile_callback( $value, $operator );
}
function oxy_toolbox_conditions_is_tablet_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_tablet_callback( $value, $operator );
}
function oxy_toolbox_conditions_is_iphone_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_iphone_callback( $value, $operator );
}
function oxy_toolbox_conditions_is_android_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_android_callback( $value, $operator );
}
function oxy_toolbox_conditions_is_handheld_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_handheld_callback( $value, $operator );
}

function oxy_toolbox_conditions_current_cpt_author_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::current_cpt_author_callback( $value, $operator );
}

function oxy_toolbox_conditions_is_homepage_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_homepage_callback( $value, $operator );
}

function oxy_toolbox_conditions_author_has_cpt_entry_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::author_has_cpt_entry_callback( $value, $operator );
}

function oxy_toolbox_conditions_user_has_written_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::user_has_written_callback( $value, $operator );
}

function oxy_toolbox_conditions_is_child_of_specific_page_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_child_of_specific_page_callback( $value, $operator );
}

function oxy_toolbox_conditions_is_child_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_child_callback( $value, $operator );
}

function oxy_toolbox_conditions_country_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::country_callback( $value, $operator );
}

function oxy_toolbox_conditions_month_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::month_callback( $value, $operator );
}

function oxy_toolbox_conditions_referrer_url_parameter_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::referrer_url_parameter_callback( $value, $operator );
}

function oxy_toolbox_conditions_archive_type_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::archive_type_callback( $value, $operator );
}

function oxy_toolbox_conditions_memberpress_membership_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::memberpress_membership_callback( $value, $operator );
}

function oxy_toolbox_conditions_rcp_membership_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::rcp_membership_callback( $value, $operator );
}

function oxy_toolbox_conditions_language_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::language_callback( $value, $operator );
}

function oxy_toolbox_conditions_body_class_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::body_class_callback( $value, $operator );
}

function oxy_toolbox_conditions_browser_detect_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::browser_detect_callback( $value, $operator );
}

function oxy_toolbox_conditions_wc_empty_cart_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::wc_empty_cart_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_in_cart_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_in_cart_callback( $value, $operator );
}
function oxy_toolbox_conditions_backorders_allowed_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::backorders_allowed_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_price_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_price_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_type_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_type_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_in_category_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_in_category_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_in_sub_categories_of_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_in_sub_categories_of_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_in_tag_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_in_tag_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_on_sale_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_on_sale_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_is_virtual_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_is_virtual_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_is_downloadable_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_is_downloadable_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_has_image_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_has_image_callback( $value, $operator );
}
function oxy_toolbox_conditions_product_in_stock_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::product_in_stock_callback( $value, $operator );
}
function oxy_toolbox_conditions_cart_weight_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::cart_weight_callback( $value, $operator );
}
function oxy_toolbox_conditions_endpoint_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::endpoint_callback( $value, $operator );
}
function oxy_toolbox_conditions_customer_bought_product_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::customer_bought_product_callback( $value, $operator );
}
function oxy_toolbox_conditions_user_purchased_product_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::user_purchased_product_callback( $value, $operator );
}
function oxy_toolbox_conditions_stock_status_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::stock_status_callback( $value, $operator );
}
function oxy_toolbox_conditions_cart_total_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::cart_total_callback( $value, $operator );
}
function oxy_toolbox_conditions_at_least_one_product_specified_tag_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::at_least_one_product_specified_tag_callback( $value, $operator );
}
function oxy_toolbox_conditions_at_least_one_product_specified_cat_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::at_least_one_product_specified_cat_callback( $value, $operator );
}
function oxy_toolbox_conditions_at_least_one_featured_product_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::at_least_one_featured_product_callback( $value, $operator );
}
function oxy_toolbox_conditions_at_least_one_product_on_sale_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::at_least_one_product_on_sale_callback( $value, $operator );
}
function oxy_toolbox_conditions_is_affiliate_affiliatewp_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_affiliate_affiliatewp_callback( $value, $operator );
}
function oxy_toolbox_conditions_http_referer_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::http_referer_callback( $value, $operator );
}
function oxy_toolbox_conditions_is_archive_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_archive_callback( $value, $operator );
}
function oxy_toolbox_conditions_post_has_tags_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::post_has_tags_callback( $value, $operator );
}
function oxy_toolbox_conditions_published_during_last_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::published_during_last_callback( $value, $operator );
}
function oxy_toolbox_conditions_polylang_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::polylang_callback( $value, $operator );
}
function oxy_toolbox_conditions_is_blog_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::is_blog_callback( $value, $operator );
}
function oxy_toolbox_conditions_date_time_callback( $value, $operator ) {
	return Oxy_Toolbox_Conditions::date_time_callback( $value, $operator );
}


Oxy_Toolbox_Conditions::init( self::PREFIX );