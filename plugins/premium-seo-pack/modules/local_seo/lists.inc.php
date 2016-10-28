<?php

global $psp;

/*
from: http://schema.org/docs/full.html

var $lb = $('a[href="../LocalBusiness"]').eq(1),
$parent = $lb.parents('table.h').eq(0),
$rows = $parent.find('> tbody > tr').filter(function(i) {
    return i>0;
});

$rows.each(function (i, elem) {
    var $that = $(this),
    $group = $that.find('> td > table.h > tbody > tr'),
    $groupTitle = $group.find('> td.tc'),
    groupTitle = $groupTitle.find('a').text(),
    $groupItems = $group.find('> td > table.h > tbody > tr');
    
    //console.log('<optgroup label="'+groupTitle+'">');
    console.log("'" + groupTitle + "' => array(");
    
    var len = $groupItems.length;
    if (len == 0)
        console.log("\t'" + groupTitle
        + "' => __('" + groupTitle + "', 'psp')");

    $groupItems.each(function (i2, e2) {
        var $item = $(this),
        $item2 = $item.find('> td.tc'),
        itemTitle = $item2.find('a').text();
        
        //console.log('\t<option value="'+itemTitle+'">'+itemTitle+'</option>');
        console.log("\t'" + itemTitle
        + "' => __('" + itemTitle + "', 'psp')"
        + ( i2 == len - 1 ? '' : ',' ));
    });
    
    //console.log('</optgroup>');
    console.log("),");
    
});

*/

$psp_business_type_list = array(
	'AnimalShelter' => array(
	
		'AnimalShelter' => __('AnimalShelter', 'psp')
	
	),
	'AutomotiveBusiness' => array(
	
		'AutoBodyShop' => __('AutoBodyShop', 'psp'),
	
		'AutoDealer' => __('AutoDealer', 'psp'),
	
		'AutoPartsStore*' => __('AutoPartsStore*', 'psp'),
	
		'AutoRental' => __('AutoRental', 'psp'),
	
		'AutoRepair' => __('AutoRepair', 'psp'),
	
		'AutoWash' => __('AutoWash', 'psp'),
	
		'GasStation' => __('GasStation', 'psp'),
	
		'MotorcycleDealer' => __('MotorcycleDealer', 'psp'),
	
		'MotorcycleRepair' => __('MotorcycleRepair', 'psp')
	
	),
	'ChildCare' => array(
	
		'ChildCare' => __('ChildCare', 'psp')
	
	),
	'DryCleaningOrLaundry' => array(
	
		'DryCleaningOrLaundry' => __('DryCleaningOrLaundry', 'psp')
	
	),
	'EmergencyService' => array(
	
		'FireStation*' => __('FireStation*', 'psp'),
	
		'Hospital*' => __('Hospital*', 'psp'),
	
		'PoliceStation*' => __('PoliceStation*', 'psp')
	
	),
	'EmploymentAgency' => array(
	
		'EmploymentAgency' => __('EmploymentAgency', 'psp')
	
	),
	'EntertainmentBusiness' => array(
	
		'AdultEntertainment' => __('AdultEntertainment', 'psp'),
	
		'AmusementPark' => __('AmusementPark', 'psp'),
	
		'ArtGallery' => __('ArtGallery', 'psp'),
	
		'Casino' => __('Casino', 'psp'),
	
		'ComedyClub' => __('ComedyClub', 'psp'),
	
		'MovieTheater*' => __('MovieTheater*', 'psp'),
	
		'NightClub' => __('NightClub', 'psp')
	
	),
	'FinancialService' => array(
	
		'AccountingService*' => __('AccountingService*', 'psp'),
	
		'AutomatedTeller' => __('AutomatedTeller', 'psp'),
	
		'BankOrCreditUnion' => __('BankOrCreditUnion', 'psp'),
	
		'InsuranceAgency' => __('InsuranceAgency', 'psp')
	
	),
	'FoodEstablishment' => array(
	
		'Bakery' => __('Bakery', 'psp'),
	
		'BarOrPub' => __('BarOrPub', 'psp'),
	
		'Brewery' => __('Brewery', 'psp'),
	
		'CafeOrCoffeeShop' => __('CafeOrCoffeeShop', 'psp'),
	
		'FastFoodRestaurant' => __('FastFoodRestaurant', 'psp'),
	
		'IceCreamShop' => __('IceCreamShop', 'psp'),
	
		'Restaurant' => __('Restaurant', 'psp'),
	
		'Winery' => __('Winery', 'psp')
	
	),
	'GovernmentOffice' => array(
	
		'PostOffice' => __('PostOffice', 'psp')
	
	),
	'HealthAndBeautyBusiness' => array(
	
		'BeautySalon' => __('BeautySalon', 'psp'),
	
		'DaySpa' => __('DaySpa', 'psp'),
	
		'HairSalon' => __('HairSalon', 'psp'),
	
		'HealthClub*' => __('HealthClub*', 'psp'),
	
		'NailSalon' => __('NailSalon', 'psp'),
	
		'TattooParlor' => __('TattooParlor', 'psp')
	
	),
	'HomeAndConstructionBusiness' => array(
	
		'Electrician*' => __('Electrician*', 'psp'),
	
		'GeneralContractor*' => __('GeneralContractor*', 'psp'),
	
		'HVACBusiness' => __('HVACBusiness', 'psp'),
	
		'HousePainter*' => __('HousePainter*', 'psp'),
	
		'Locksmith*' => __('Locksmith*', 'psp'),
	
		'MovingCompany' => __('MovingCompany', 'psp'),
	
		'Plumber*' => __('Plumber*', 'psp'),
	
		'RoofingContractor*' => __('RoofingContractor*', 'psp')
	
	),
	'InternetCafe' => array(
	
		'InternetCafe' => __('InternetCafe', 'psp')
	
	),
	'Library' => array(
	
		'Library' => __('Library', 'psp')
	
	),
	'LodgingBusiness' => array(
	
		'BedAndBreakfast' => __('BedAndBreakfast', 'psp'),
	
		'Hostel' => __('Hostel', 'psp'),
	
		'Hotel' => __('Hotel', 'psp'),
	
		'Motel' => __('Motel', 'psp')
	
	),
	'MedicalOrganization' => array(
	
		'Dentist*' => __('Dentist*', 'psp'),
	
		'DiagnosticLab' => __('DiagnosticLab', 'psp'),
	
		'Hospital*' => __('Hospital*', 'psp'),
	
		'MedicalClinic' => __('MedicalClinic', 'psp'),
	
		'Optician' => __('Optician', 'psp'),
	
		'Pharmacy' => __('Pharmacy', 'psp'),
	
		'Physician' => __('Physician', 'psp'),
	
		'VeterinaryCare' => __('VeterinaryCare', 'psp')
	
	),
	'ProfessionalService' => array(
	
		'AccountingService*' => __('AccountingService*', 'psp'),
	
		'Attorney' => __('Attorney', 'psp'),
	
		'Dentist*' => __('Dentist*', 'psp'),
	
		'Electrician*' => __('Electrician*', 'psp'),
	
		'GeneralContractor*' => __('GeneralContractor*', 'psp'),
	
		'HousePainter*' => __('HousePainter*', 'psp'),
	
		'Locksmith*' => __('Locksmith*', 'psp'),
	
		'Notary' => __('Notary', 'psp'),
	
		'Plumber*' => __('Plumber*', 'psp'),
	
		'RoofingContractor*' => __('RoofingContractor*', 'psp')
	
	),
	'RadioStation' => array(
	
		'RadioStation' => __('RadioStation', 'psp')
	
	),
	'RealEstateAgent' => array(
	
		'RealEstateAgent' => __('RealEstateAgent', 'psp')
	
	),
	'RecyclingCenter' => array(
	
		'RecyclingCenter' => __('RecyclingCenter', 'psp')
	
	),
	'SelfStorage' => array(
	
		'SelfStorage' => __('SelfStorage', 'psp')
	
	),
	'ShoppingCenter' => array(
	
		'ShoppingCenter' => __('ShoppingCenter', 'psp')
	
	),
	'SportsActivityLocation' => array(
	
		'BowlingAlley' => __('BowlingAlley', 'psp'),
	
		'ExerciseGym' => __('ExerciseGym', 'psp'),
	
		'GolfCourse' => __('GolfCourse', 'psp'),
	
		'HealthClub*' => __('HealthClub*', 'psp'),
	
		'PublicSwimmingPool' => __('PublicSwimmingPool', 'psp'),
	
		'SkiResort' => __('SkiResort', 'psp'),
	
		'SportsClub' => __('SportsClub', 'psp'),
	
		'StadiumOrArena*' => __('StadiumOrArena*', 'psp'),
	
		'TennisComplex' => __('TennisComplex', 'psp')
	
	),
	'Store' => array(
	
		'AutoPartsStore*' => __('AutoPartsStore*', 'psp'),
	
		'BikeStore' => __('BikeStore', 'psp'),
	
		'BookStore' => __('BookStore', 'psp'),
	
		'ClothingStore' => __('ClothingStore', 'psp'),
	
		'ComputerStore' => __('ComputerStore', 'psp'),
	
		'ConvenienceStore' => __('ConvenienceStore', 'psp'),
	
		'DepartmentStore' => __('DepartmentStore', 'psp'),
	
		'ElectronicsStore' => __('ElectronicsStore', 'psp'),
	
		'Florist' => __('Florist', 'psp'),
	
		'FurnitureStore' => __('FurnitureStore', 'psp'),
	
		'GardenStore' => __('GardenStore', 'psp'),
	
		'GroceryStore' => __('GroceryStore', 'psp'),
	
		'HardwareStore' => __('HardwareStore', 'psp'),
	
		'HobbyShop' => __('HobbyShop', 'psp'),
	
		'HomeGoodsStore' => __('HomeGoodsStore', 'psp'),
	
		'JewelryStore' => __('JewelryStore', 'psp'),
	
		'LiquorStore' => __('LiquorStore', 'psp'),
	
		'MensClothingStore' => __('MensClothingStore', 'psp'),
	
		'MobilePhoneStore' => __('MobilePhoneStore', 'psp'),
	
		'MovieRentalStore' => __('MovieRentalStore', 'psp'),
	
		'MusicStore' => __('MusicStore', 'psp'),
	
		'OfficeEquipmentStore' => __('OfficeEquipmentStore', 'psp'),
	
		'OutletStore' => __('OutletStore', 'psp'),
	
		'PawnShop' => __('PawnShop', 'psp'),
	
		'PetStore' => __('PetStore', 'psp'),
	
		'ShoeStore' => __('ShoeStore', 'psp'),
	
		'SportingGoodsStore' => __('SportingGoodsStore', 'psp'),
	
		'TireShop' => __('TireShop', 'psp'),
	
		'ToyStore' => __('ToyStore', 'psp'),
	
		'WholesaleStore' => __('WholesaleStore', 'psp')
	
	),
	'TelevisionStation' => array(
	
		'TelevisionStation' => __('TelevisionStation', 'psp')
	
	),
	'TouristInformationCenter' => array(
	
		'TouristInformationCenter' => __('TouristInformationCenter', 'psp')
	
	),
	'TravelAgency' => array(
	
		'TravelAgency' => __('TravelAgency', 'psp')
	
	)
);


/*
from: http://www.state.gov/misc/list/

var $blocks = $('blockquote[dir="ltr"]');
$blocks.each(function (i,e) {
    var $that = $(this), $rows = $that.find('> p > a');
    $rows.each(function (i2, e2) {
        var country = $(this).text();
        country = $.trim(country);
        country = country.replace(/'/g, "\\'");
        console.log( "'" + country + "' => '" + country
        + "',");
    });
});
*/

$psp_countries_list = array(
	'Afghanistan' => 'Afghanistan',
	'Albania' => 'Albania',
	'Algeria' => 'Algeria',
	'Andorra' => 'Andorra',
	'Angola' => 'Angola',
	'Antigua and Barbuda' => 'Antigua and Barbuda',
	'Argentina' => 'Argentina',
	'Armenia' => 'Armenia',
	'Aruba' => 'Aruba',
	'Australia' => 'Australia',
	'Austria' => 'Austria',
	'Azerbaijan' => 'Azerbaijan',
	'Bahamas, The' => 'Bahamas, The',
	'Bahrain' => 'Bahrain',
	'Bangladesh' => 'Bangladesh',
	'Barbados' => 'Barbados',
	'Belarus' => 'Belarus',
	'Belgium' => 'Belgium',
	'Belize' => 'Belize',
	'Benin' => 'Benin',
	'Bhutan' => 'Bhutan',
	'Bolivia' => 'Bolivia',
	'Bosnia and Herzegovina' => 'Bosnia and Herzegovina',
	'Botswana' => 'Botswana',
	'Brazil' => 'Brazil',
	'Brunei' => 'Brunei',
	'Bulgaria' => 'Bulgaria',
	'Burkina Faso' => 'Burkina Faso',
	'Burma' => 'Burma',
	'Burundi' => 'Burundi',
	'Cambodia' => 'Cambodia',
	'Cameroon' => 'Cameroon',
	'Canada' => 'Canada',
	'Cape Verde' => 'Cape Verde',
	'Central African Republic' => 'Central African Republic',
	'Chad' => 'Chad',
	'Chile' => 'Chile',
	'China' => 'China',
	'Colombia' => 'Colombia',
	'Comoros' => 'Comoros',
	'Congo, Democratic Republic of the' => 'Congo, Democratic Republic of the',
	'Congo, Republic of the' => 'Congo, Republic of the',
	'Costa Rica' => 'Costa Rica',
	'Cote d\'Ivoire' => 'Cote d\'Ivoire',
	'Croatia' => 'Croatia',
	'Cuba' => 'Cuba',
	'Curacao' => 'Curacao',
	'Cyprus' => 'Cyprus',
	'Czech Republic' => 'Czech Republic',
	'Denmark' => 'Denmark',
	'Djibouti' => 'Djibouti',
	'Dominica' => 'Dominica',
	'Dominican Republic' => 'Dominican Republic',
	'Timor-Leste' => 'Timor-Leste',
	'Ecuador' => 'Ecuador',
	'Egypt' => 'Egypt',
	'El Salvador' => 'El Salvador',
	'Equatorial Guinea' => 'Equatorial Guinea',
	'Eritrea' => 'Eritrea',
	'Estonia' => 'Estonia',
	'Ethiopia' => 'Ethiopia',
	'Fiji' => 'Fiji',
	'Finland' => 'Finland',
	'France' => 'France',
	'Gabon' => 'Gabon',
	'Gambia, The' => 'Gambia, The',
	'Georgia' => 'Georgia',
	'Germany' => 'Germany',
	'Ghana' => 'Ghana',
	'Greece' => 'Greece',
	'Grenada' => 'Grenada',
	'Guatemala' => 'Guatemala',
	'Guinea' => 'Guinea',
	'Guinea-Bissau' => 'Guinea-Bissau',
	'Guyana' => 'Guyana',
	'Haiti' => 'Haiti',
	'Holy See' => 'Holy See',
	'Honduras' => 'Honduras',
	'Hong Kong' => 'Hong Kong',
	'Hungary' => 'Hungary',
	'Iceland' => 'Iceland',
	'India' => 'India',
	'Indonesia' => 'Indonesia',
	'Iran' => 'Iran',
	'Iraq' => 'Iraq',
	'Ireland' => 'Ireland',
	'Israel' => 'Israel',
	'Italy' => 'Italy',
	'Jamaica' => 'Jamaica',
	'Japan' => 'Japan',
	'Jordan' => 'Jordan',
	'Kazakhstan' => 'Kazakhstan',
	'Kenya' => 'Kenya',
	'Kiribati' => 'Kiribati',
	'Korea, North' => 'Korea, North',
	'Korea, South' => 'Korea, South',
	'Kosovo' => 'Kosovo',
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
	'Macau' => 'Macau',
	'Macedonia' => 'Macedonia',
	'Madagascar' => 'Madagascar',
	'Malawi' => 'Malawi',
	'Malaysia' => 'Malaysia',
	'Maldives' => 'Maldives',
	'Mali' => 'Mali',
	'Malta' => 'Malta',
	'Marshall Islands' => 'Marshall Islands',
	'Mauritania' => 'Mauritania',
	'Mauritius' => 'Mauritius',
	'Mexico' => 'Mexico',
	'Micronesia' => 'Micronesia',
	'Moldova' => 'Moldova',
	'Monaco' => 'Monaco',
	'Mongolia' => 'Mongolia',
	'Montenegro' => 'Montenegro',
	'Morocco' => 'Morocco',
	'Mozambique' => 'Mozambique',
	'Namibia' => 'Namibia',
	'Nauru' => 'Nauru',
	'Nepal' => 'Nepal',
	'Netherlands' => 'Netherlands',
	'Netherlands Antilles' => 'Netherlands Antilles',
	'New Zealand' => 'New Zealand',
	'Nicaragua' => 'Nicaragua',
	'Niger' => 'Niger',
	'Nigeria' => 'Nigeria',
	'North Korea' => 'North Korea',
	'Norway' => 'Norway',
	'Oman' => 'Oman',
	'Pakistan' => 'Pakistan',
	'Palau' => 'Palau',
	'Palestinian Territories' => 'Palestinian Territories',
	'Panama' => 'Panama',
	'Papua New Guinea' => 'Papua New Guinea',
	'Paraguay' => 'Paraguay',
	'Peru' => 'Peru',
	'Philippines' => 'Philippines',
	'Poland' => 'Poland',
	'Portugal' => 'Portugal',
	'Qatar' => 'Qatar',
	'Romania' => 'Romania',
	'Russia' => 'Russia',
	'Rwanda' => 'Rwanda',
	'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
	'Saint Lucia' => 'Saint Lucia',
	'Saint Vincent and the Grenadines' => 'Saint Vincent and the Grenadines',
	'Samoa' => 'Samoa',
	'San Marino' => 'San Marino',
	'Sao Tome and Principe' => 'Sao Tome and Principe',
	'Saudi Arabia' => 'Saudi Arabia',
	'Senegal' => 'Senegal',
	'Serbia' => 'Serbia',
	'Seychelles' => 'Seychelles',
	'Sierra Leone' => 'Sierra Leone',
	'Singapore' => 'Singapore',
	'Sint Maarten' => 'Sint Maarten',
	'Slovakia' => 'Slovakia',
	'Slovenia' => 'Slovenia',
	'Solomon Islands' => 'Solomon Islands',
	'Somalia' => 'Somalia',
	'South Africa' => 'South Africa',
	'South Korea' => 'South Korea',
	'South Sudan' => 'South Sudan',
	'Spain' => 'Spain',
	'Sri Lanka' => 'Sri Lanka',
	'Sudan' => 'Sudan',
	'Suriname' => 'Suriname',
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
	'Tonga' => 'Tonga',
	'Trinidad and Tobago' => 'Trinidad and Tobago',
	'Tunisia' => 'Tunisia',
	'Turkey' => 'Turkey',
	'Turkmenistan' => 'Turkmenistan',
	'Tuvalu' => 'Tuvalu',
	'Uganda' => 'Uganda',
	'Ukraine' => 'Ukraine',
	'United Arab Emirates' => 'United Arab Emirates',
	'United Kingdom' => 'United Kingdom',
	'United States of America' => 'United States of America',
	'Uruguay' => 'Uruguay',
	'Uzbekistan' => 'Uzbekistan',
	'Vanuatu' => 'Vanuatu',
	'Venezuela' => 'Venezuela',
	'Vietnam' => 'Vietnam',
	'Yemen' => 'Yemen',
	'Zambia' => 'Zambia',
	'Zimbabwe' => 'Zimbabwe'
);


$psp_days_list = array(
	'monday'		=> __('Monday', 'psp'),
	'tuesday'		=> __('Tuesday', 'psp'),
	'wednesday'		=> __('Wednesday', 'psp'),
	'thursday'		=> __('Thursday', 'psp'),
	'friday'		=> __('Friday', 'psp'),
	'saturday'		=> __('Saturday', 'psp'),
	'sunday'		=> __('Sunday', 'psp')
);

?>