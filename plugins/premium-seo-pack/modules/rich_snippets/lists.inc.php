<?php

global $psp;

/*
(function ($) {
    var $list = $('#schema_country');
    $list.find('option').each(function(i,e) {
        var country = $(this).text();
        country = $.trim(country);
        country = country.replace(/'/g, "\\'");
        console.log( "'" + country + "' => '" + country
        + "',");
    })
})(jQuery);
*/
$psp_countries_list = array(
	'United States' => 'United States',
	'Canada' => 'Canada',
	'Mexico' => 'Mexico',
	'United Kingdom' => 'United Kingdom',
	'Afghanistan' => 'Afghanistan',
	'Aland Islands' => 'Aland Islands',
	'Albania' => 'Albania',
	'Algeria' => 'Algeria',
	'American Samoa' => 'American Samoa',
	'Andorra' => 'Andorra',
	'Angola' => 'Angola',
	'Anguilla' => 'Anguilla',
	'Antarctica' => 'Antarctica',
	'Antigua And Barbuda' => 'Antigua And Barbuda',
	'Argentina' => 'Argentina',
	'Armenia' => 'Armenia',
	'Aruba' => 'Aruba',
	'Australia' => 'Australia',
	'Austria' => 'Austria',
	'Azerbaijan' => 'Azerbaijan',
	'Bahamas' => 'Bahamas',
	'Bahrain' => 'Bahrain',
	'Bangladesh' => 'Bangladesh',
	'Barbados' => 'Barbados',
	'Belarus' => 'Belarus',
	'Belgium' => 'Belgium',
	'Belize' => 'Belize',
	'Benin' => 'Benin',
	'Bermuda' => 'Bermuda',
	'Bhutan' => 'Bhutan',
	'Bolivia, Plurinational State Of' => 'Bolivia, Plurinational State Of',
	'Bonaire, Sint Eustatius And Saba' => 'Bonaire, Sint Eustatius And Saba',
	'Bosnia And Herzegovina' => 'Bosnia And Herzegovina',
	'Botswana' => 'Botswana',
	'Bouvet Island' => 'Bouvet Island',
	'Brazil' => 'Brazil',
	'British Indian Ocean Territory' => 'British Indian Ocean Territory',
	'British Virgin Islands' => 'British Virgin Islands',
	'Brunei Darussalam' => 'Brunei Darussalam',
	'Bulgaria' => 'Bulgaria',
	'Burkina Faso' => 'Burkina Faso',
	'Burundi' => 'Burundi',
	'Cambodia' => 'Cambodia',
	'Cameroon' => 'Cameroon',
	'Cape Verde' => 'Cape Verde',
	'Cayman Islands' => 'Cayman Islands',
	'Central African Republic' => 'Central African Republic',
	'Chad' => 'Chad',
	'Chile' => 'Chile',
	'China' => 'China',
	'Christmas Island' => 'Christmas Island',
	'Cocos (Keeling) Islands' => 'Cocos (Keeling) Islands',
	'Colombia' => 'Colombia',
	'Comoros' => 'Comoros',
	'Congo' => 'Congo',
	'Congo, The Democratic Republic Of The' => 'Congo, The Democratic Republic Of The',
	'Cook Islands' => 'Cook Islands',
	'Costa Rica' => 'Costa Rica',
	'Cote D\'Ivoire' => 'Cote D\'Ivoire',
	'Croatia' => 'Croatia',
	'Cuba' => 'Cuba',
	'Curacao' => 'Curacao',
	'Cyprus' => 'Cyprus',
	'Czech Republic' => 'Czech Republic',
	'Denmark' => 'Denmark',
	'Djibouti' => 'Djibouti',
	'Dominica' => 'Dominica',
	'Dominican Republic' => 'Dominican Republic',
	'Ecuador' => 'Ecuador',
	'Egypt' => 'Egypt',
	'El Salvador' => 'El Salvador',
	'Equatorial Guinea' => 'Equatorial Guinea',
	'Eritrea' => 'Eritrea',
	'Estonia' => 'Estonia',
	'Ethiopia' => 'Ethiopia',
	'Falkland Islands (Malvinas)' => 'Falkland Islands (Malvinas)',
	'Faroe Islands' => 'Faroe Islands',
	'Fiji' => 'Fiji',
	'Finland' => 'Finland',
	'France' => 'France',
	'French Guiana' => 'French Guiana',
	'French Polynesia' => 'French Polynesia',
	'French Southern Territories' => 'French Southern Territories',
	'Gabon' => 'Gabon',
	'Gambia' => 'Gambia',
	'Georgia' => 'Georgia',
	'Germany' => 'Germany',
	'Ghana' => 'Ghana',
	'Gibraltar' => 'Gibraltar',
	'Greece' => 'Greece',
	'Greenland' => 'Greenland',
	'Grenada' => 'Grenada',
	'Guadeloupe' => 'Guadeloupe',
	'Guam' => 'Guam',
	'Guatemala' => 'Guatemala',
	'Guernsey' => 'Guernsey',
	'Guinea' => 'Guinea',
	'Guinea-Bissau' => 'Guinea-Bissau',
	'Guyana' => 'Guyana',
	'Haiti' => 'Haiti',
	'Heard Island And Mcdonald Islands' => 'Heard Island And Mcdonald Islands',
	'Honduras' => 'Honduras',
	'Hong Kong' => 'Hong Kong',
	'Hungary' => 'Hungary',
	'Iceland' => 'Iceland',
	'India' => 'India',
	'Indonesia' => 'Indonesia',
	'Iran' => 'Iran',
	'Iraq' => 'Iraq',
	'Ireland' => 'Ireland',
	'Isle Of Man' => 'Isle Of Man',
	'Israel' => 'Israel',
	'Italy' => 'Italy',
	'Jamaica' => 'Jamaica',
	'Japan' => 'Japan',
	'Jersey' => 'Jersey',
	'Jordan' => 'Jordan',
	'Kazakhstan' => 'Kazakhstan',
	'Kenya' => 'Kenya',
	'Kiribati' => 'Kiribati',
	'Kuwait' => 'Kuwait',
	'Kyrgyzstan' => 'Kyrgyzstan',
	'Laos' => 'Laos',
	'Latvia' => 'Latvia',
	'Lebanon' => 'Lebanon',
	'Lesotho' => 'Lesotho',
	'Liberia' => 'Liberia',
	'Libya' => 'Libya',
	'Liechtenstein' => 'Liechtenstein',
	'Lithuania' => 'Lithuania',
	'Luxembourg' => 'Luxembourg',
	'Macao' => 'Macao',
	'Macedonia' => 'Macedonia',
	'Madagascar' => 'Madagascar',
	'Malawi' => 'Malawi',
	'Malaysia' => 'Malaysia',
	'Maldives' => 'Maldives',
	'Mali' => 'Mali',
	'Malta' => 'Malta',
	'Marshall Islands' => 'Marshall Islands',
	'Martinique' => 'Martinique',
	'Mauritania' => 'Mauritania',
	'Mauritius' => 'Mauritius',
	'Mayotte' => 'Mayotte',
	'Micronesia' => 'Micronesia',
	'Moldova' => 'Moldova',
	'Monaco' => 'Monaco',
	'Mongolia' => 'Mongolia',
	'Montenegro' => 'Montenegro',
	'Montserrat' => 'Montserrat',
	'Morocco' => 'Morocco',
	'Mozambique' => 'Mozambique',
	'Myanmar' => 'Myanmar',
	'Namibia' => 'Namibia',
	'Nauru' => 'Nauru',
	'Nepal' => 'Nepal',
	'Netherlands' => 'Netherlands',
	'New Caledonia' => 'New Caledonia',
	'New Zealand' => 'New Zealand',
	'Nicaragua' => 'Nicaragua',
	'Niger' => 'Niger',
	'Nigeria' => 'Nigeria',
	'Niue' => 'Niue',
	'Norfolk Island' => 'Norfolk Island',
	'North Korea' => 'North Korea',
	'Northern Mariana Islands' => 'Northern Mariana Islands',
	'Norway' => 'Norway',
	'Oman' => 'Oman',
	'Pakistan' => 'Pakistan',
	'Palau' => 'Palau',
	'Palestine' => 'Palestine',
	'Panama' => 'Panama',
	'Papua New Guinea' => 'Papua New Guinea',
	'Paraguay' => 'Paraguay',
	'Peru' => 'Peru',
	'Philippines' => 'Philippines',
	'Pitcairn' => 'Pitcairn',
	'Poland' => 'Poland',
	'Portugal' => 'Portugal',
	'Puerto Rico' => 'Puerto Rico',
	'Qatar' => 'Qatar',
	'Reunion' => 'Reunion',
	'Romania' => 'Romania',
	'Russian Federation' => 'Russian Federation',
	'Rwanda' => 'Rwanda',
	'Samoa' => 'Samoa',
	'San Marino' => 'San Marino',
	'Sao Tome And Principe' => 'Sao Tome And Principe',
	'Saudi Arabia' => 'Saudi Arabia',
	'Senegal' => 'Senegal',
	'Serbia' => 'Serbia',
	'Seychelles' => 'Seychelles',
	'Sierra Leone' => 'Sierra Leone',
	'Singapore' => 'Singapore',
	'Sint Maarten (Dutch Part)' => 'Sint Maarten (Dutch Part)',
	'Slovakia' => 'Slovakia',
	'Slovenia' => 'Slovenia',
	'Solomon Islands' => 'Solomon Islands',
	'Somalia' => 'Somalia',
	'South Africa' => 'South Africa',
	'South Georgia' => 'South Georgia',
	'South Korea' => 'South Korea',
	'South Sudan' => 'South Sudan',
	'Spain' => 'Spain',
	'Sri Lanka' => 'Sri Lanka',
	'St. Barthelemy' => 'St. Barthelemy',
	'St. Helena' => 'St. Helena',
	'St. Kitts And Nevis' => 'St. Kitts And Nevis',
	'St. Lucia' => 'St. Lucia',
	'St. Martin (French Part)' => 'St. Martin (French Part)',
	'St. Pierre And Miquelon' => 'St. Pierre And Miquelon',
	'St. Vincent And The Grenadines' => 'St. Vincent And The Grenadines',
	'Sudan' => 'Sudan',
	'Suriname' => 'Suriname',
	'Svalbard' => 'Svalbard',
	'Swaziland' => 'Swaziland',
	'Sweden' => 'Sweden',
	'Switzerland' => 'Switzerland',
	'Syria' => 'Syria',
	'Taiwan' => 'Taiwan',
	'Tajikistan' => 'Tajikistan',
	'Tanzania' => 'Tanzania',
	'Thailand' => 'Thailand',
	'Timor-Leste' => 'Timor-Leste',
	'Togo' => 'Togo',
	'Tokelau' => 'Tokelau',
	'Tonga' => 'Tonga',
	'Trinidad And Tobago' => 'Trinidad And Tobago',
	'Tunisia' => 'Tunisia',
	'Turkey' => 'Turkey',
	'Turkmenistan' => 'Turkmenistan',
	'Turks And Caicos Islands' => 'Turks And Caicos Islands',
	'Tuvalu' => 'Tuvalu',
	'U.S. Virgin Islands' => 'U.S. Virgin Islands',
	'Uganda' => 'Uganda',
	'Ukraine' => 'Ukraine',
	'United Arab Emirates' => 'United Arab Emirates',
	'United States Minor Outlying Islands' => 'United States Minor Outlying Islands',
	'Uruguay' => 'Uruguay',
	'Uzbekistan' => 'Uzbekistan',
	'Vanuatu' => 'Vanuatu',
	'Vatican City' => 'Vatican City',
	'Venezuela' => 'Venezuela',
	'Vietnam' => 'Vietnam',
	'Wallis And Futuna' => 'Wallis And Futuna',
	'Western Sahara' => 'Western Sahara',
	'Yemen' => 'Yemen',
	'Zambia' => 'Zambia',
	'Zimbabwe' => 'Zimbabwe'
);

/*
(function ($) {
    var $list = $('#schema_evtype');
    $list.find('option').each(function(i,e) {
        var country = $(this).text();
        country = $.trim(country);
        country = country.replace(/'/g, "\\'");
        console.log( "'" + $(this).val() + "' => '" + country
        + "',");
    })
})(jQuery);
*/
$psp_event_type = array(
	'Event' => __('General', 'psp'),
	'BusinessEvent' => __('Business', 'psp'),
	'ChildrensEvent' => __('Childrens', 'psp'),
	'ComedyEvent' => __('Comedy', 'psp'),
	'DanceEvent' => __('Dance', 'psp'),
	'EducationEvent' => __('Education', 'psp'),
	'Festival' => __('Festival', 'psp'),
	'FoodEvent' => __('Food', 'psp'),
	'LiteraryEvent' => __('Literary', 'psp'),
	'MusicEvent' => __('Music', 'psp'),
	'SaleEvent' => __('Sale', 'psp'),
	'SocialEvent' => __('Social', 'psp'),
	'SportsEvent' => __('Sports', 'psp'),
	'TheaterEvent' => __('Theater', 'psp'),
	'UserInteraction' => __('User Interaction', 'psp'),
	'VisualArtsEvent' => __('Visual Arts', 'psp')
);

/*
(function ($) {
    var $list = $('#schema_orgtype');
    $list.find('option').each(function(i,e) {
        var country = $(this).text();
        country = $.trim(country);
        country = country.replace(/'/g, "\\'");
        console.log( "'" + $(this).val() + "' => '" + country
        + "',");
    })
})(jQuery);
*/
$psp_organization_type = array(
	'Organization' => __('General', 'psp'),
	'Corporation' => __('Corporation', 'psp'),
	'EducationalOrganization' => __('School', 'psp'),
	'GovernmentOrganization' => __('Government', 'psp'),
	'LocalBusiness' => __('Local Business', 'psp'),
	'NGO' => __('NGO', 'psp'),
	'PerformingGroup' => __('Performing Group', 'psp'),
	'SportsTeam' => __('Sports Team', 'psp')
);

/*
(function ($) {
    var $list = $('#schema_condition');
    $list.find('option').each(function(i,e) {
        var country = $(this).text();
        country = $.trim(country);
        country = country.replace(/'/g, "\\'");
        console.log( "'" + $(this).val() + "' => '" + country
        + "',");
    })
})(jQuery);
*/
$psp_product_condition = array(
	'New' => __('New', 'psp'),
	'Used' => __('Used', 'psp'),
	'Refurbished' => __('Refurbished', 'psp'),
	'Damaged' => __('Damaged', 'psp')
);

/*
(function ($) {
    var $list = $('#_bsf_product_status');
    $list.find('option').each(function(i,e) {
        var country = $(this).text();
        country = $.trim(country);
        country = country.replace(/'/g, "\\'");
        console.log( "'" + $(this).val() + "' => '" + country
        + "',");
    })
})(jQuery);
*/
$psp_product_availability = array(
	'Discontinued' => __('Discontinued', 'psp'),
	'InStock' => __('In Stock', 'psp'),
	'InStoreOnly' => __('In Store Only', 'psp'),
	'LimitedAvailability' => __('Limited Availability', 'psp'),
	'OnlineOnly' => __('Online Only', 'psp'),
	'OutOfStock' => __('Out Of Stock', 'psp'),
	'PreOrder' => __('Pre Order', 'psp'),
	'SoldOut' => __('Sold Out', 'psp')
);

/*
static
*/
$psp_book_formats = array(
	'EBook'			=> __('EBook', 'psp'),
	'Paperback'		=> __('Paperback', 'psp'),
	'Hardcover'		=> __('Hardcover', 'psp')
);

?>