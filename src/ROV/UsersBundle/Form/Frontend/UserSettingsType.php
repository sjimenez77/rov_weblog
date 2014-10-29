<?php
// src/ROV/UsersBundle/Form/Frontend/UserSettingsType.php
namespace ROV\UsersBundle\Form\Frontend;

use ROV\UsersBundle\Form\Frontend\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Edit user profile form
 */
class UserSettingsType extends UserType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
            ->add('name', 'text', array(
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your name',
                    )
                ))
            ->add('surname', 'text', array(
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your surname',
                    )
                ))
            ->add('email', 'email', array(
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your email',
                    )
                ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_options'  => array(
                    'label' => 'Password',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type your password'
                        )
                    ),
                'second_options' => array(
                    'label' => 'Repeat Password',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type your password once more'
                        )
                    ),
                'required' => false
                ))
            ->add('city', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            /* php_intl no soportado en Hostinger
            ->add('country', 'country', array(
                'attr' => array('class' => 'form-control'),
                'empty_value' => ''
                ))
            */
            ->add('country', 'choice', array(
                'attr' => array('class' => 'form-control'),
                'empty_value' => '',
                'choices' => array(
                    'AF' => 'Afghanistan',
                    'AL' => 'Albania',
                    'DZ' => 'Algeria',
                    'AS' => 'American Samoa',
                    'AD' => 'Andorra',
                    'AO' => 'Angola',
                    'AI' => 'Anguilla',
                    'AQ' => 'Antarctica',
                    'AG' => 'Antigua And Barbuda',
                    'AR' => 'Argentina',
                    'AM' => 'Armenia',
                    'AW' => 'Aruba',
                    'AU' => 'Australia',
                    'AT' => 'Austria',
                    'AZ' => 'Azerbaijan',
                    'BS' => 'Bahamas',
                    'BH' => 'Bahrain',
                    'BD' => 'Bangladesh',
                    'BB' => 'Barbados',
                    'BY' => 'Belarus',
                    'BE' => 'Belgium',
                    'BZ' => 'Belize',
                    'BJ' => 'Benin',
                    'BM' => 'Bermuda',
                    'BT' => 'Bhutan',
                    'BO' => 'Bolivia',
                    'BA' => 'Bosnia And Herzegowina',
                    'BW' => 'Botswana',
                    'BV' => 'Bouvet Island',
                    'BR' => 'Brazil',
                    'IO' => 'British Indian Ocean Territory',
                    'BN' => 'Brunei Darussalam',
                    'BG' => 'Bulgaria',
                    'BF' => 'Burkina Faso',
                    'BI' => 'Burundi',
                    'KH' => 'Cambodia',
                    'CM' => 'Cameroon',
                    'CA' => 'Canada',
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
                    'CG' => 'Congo',
                    'CD' => 'Congo, The Democratic Republic Of The',
                    'CK' => 'Cook Islands',
                    'CR' => 'Costa Rica',
                    'CI' => 'Cote D\'Ivoire',
                    'HR' => 'Croatia (Local Name: Hrvatska)',
                    'CU' => 'Cuba',
                    'CY' => 'Cyprus',
                    'CZ' => 'Czech Republic',
                    'DK' => 'Denmark',
                    'DJ' => 'Djibouti',
                    'DM' => 'Dominica',
                    'DO' => 'Dominican Republic',
                    'TP' => 'East Timor',
                    'EC' => 'Ecuador',
                    'EG' => 'Egypt',
                    'SV' => 'El Salvador',
                    'GQ' => 'Equatorial Guinea',
                    'ER' => 'Eritrea',
                    'EE' => 'Estonia',
                    'ET' => 'Ethiopia',
                    'FK' => 'Falkland Islands (Malvinas)',
                    'FO' => 'Faroe Islands',
                    'FJ' => 'Fiji',
                    'FI' => 'Finland',
                    'FR' => 'France',
                    'FX' => 'France, Metropolitan',
                    'GF' => 'French Guiana',
                    'PF' => 'French Polynesia',
                    'TF' => 'French Southern Territories',
                    'GA' => 'Gabon',
                    'GM' => 'Gambia',
                    'GE' => 'Georgia',
                    'DE' => 'Germany',
                    'GH' => 'Ghana',
                    'GI' => 'Gibraltar',
                    'GR' => 'Greece',
                    'GL' => 'Greenland',
                    'GD' => 'Grenada',
                    'GP' => 'Guadeloupe',
                    'GU' => 'Guam',
                    'GT' => 'Guatemala',
                    'GN' => 'Guinea',
                    'GW' => 'Guinea-Bissau',
                    'GY' => 'Guyana',
                    'HT' => 'Haiti',
                    'HM' => 'Heard And Mc Donald Islands',
                    'VA' => 'Holy See (Vatican City State)',
                    'HN' => 'Honduras',
                    'HK' => 'Hong Kong',
                    'HU' => 'Hungary',
                    'IS' => 'Iceland',
                    'IN' => 'India',
                    'ID' => 'Indonesia',
                    'IR' => 'Iran (Islamic Republic Of)',
                    'IQ' => 'Iraq',
                    'IE' => 'Ireland',
                    'IL' => 'Israel',
                    'IT' => 'Italy',
                    'JM' => 'Jamaica',
                    'JP' => 'Japan',
                    'JO' => 'Jordan',
                    'KZ' => 'Kazakhstan',
                    'KE' => 'Kenya',
                    'KI' => 'Kiribati',
                    'KP' => 'Korea, Democratic People\'S Republic Of',
                    'KR' => 'Korea, Republic Of',
                    'KW' => 'Kuwait',
                    'KG' => 'Kyrgyzstan',
                    'LA' => 'Lao People\'S Democratic Republic',
                    'LV' => 'Latvia',
                    'LB' => 'Lebanon',
                    'LS' => 'Lesotho',
                    'LR' => 'Liberia',
                    'LY' => 'Libyan Arab Jamahiriya',
                    'LI' => 'Liechtenstein',
                    'LT' => 'Lithuania',
                    'LU' => 'Luxembourg',
                    'MO' => 'Macau',
                    'MK' => 'Macedonia, Former Yugoslav Republic Of',
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
                    'FM' => 'Micronesia, Federated States Of',
                    'MD' => 'Moldova, Republic Of',
                    'MC' => 'Monaco',
                    'MN' => 'Mongolia',
                    'MS' => 'Montserrat',
                    'MA' => 'Morocco',
                    'MZ' => 'Mozambique',
                    'MM' => 'Myanmar',
                    'NA' => 'Namibia',
                    'NR' => 'Nauru',
                    'NP' => 'Nepal',
                    'NL' => 'Netherlands',
                    'AN' => 'Netherlands Antilles',
                    'NC' => 'New Caledonia',
                    'NZ' => 'New Zealand',
                    'NI' => 'Nicaragua',
                    'NE' => 'Niger',
                    'NG' => 'Nigeria',
                    'NU' => 'Niue',
                    'NF' => 'Norfolk Island',
                    'MP' => 'Northern Mariana Islands',
                    'NO' => 'Norway',
                    'OM' => 'Oman',
                    'PK' => 'Pakistan',
                    'PW' => 'Palau',
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
                    'RU' => 'Russian Federation',
                    'RW' => 'Rwanda',
                    'KN' => 'Saint Kitts And Nevis',
                    'LC' => 'Saint Lucia',
                    'VC' => 'Saint Vincent And The Grenadines',
                    'WS' => 'Samoa',
                    'SM' => 'San Marino',
                    'ST' => 'Sao Tome And Principe',
                    'SA' => 'Saudi Arabia',
                    'SN' => 'Senegal',
                    'SC' => 'Seychelles',
                    'SL' => 'Sierra Leone',
                    'SG' => 'Singapore',
                    'SK' => 'Slovakia (Slovak Republic)',
                    'SI' => 'Slovenia',
                    'SB' => 'Solomon Islands',
                    'SO' => 'Somalia',
                    'ZA' => 'South Africa',
                    'GS' => 'South Georgia, South Sandwich Islands',
                    'ES' => 'Spain',
                    'LK' => 'Sri Lanka',
                    'SH' => 'St. Helena',
                    'PM' => 'St. Pierre And Miquelon',
                    'SD' => 'Sudan',
                    'SR' => 'Suriname',
                    'SJ' => 'Svalbard And Jan Mayen Islands',
                    'SZ' => 'Swaziland',
                    'SE' => 'Sweden',
                    'CH' => 'Switzerland',
                    'SY' => 'Syrian Arab Republic',
                    'TW' => 'Taiwan',
                    'TJ' => 'Tajikistan',
                    'TZ' => 'Tanzania, United Republic Of',
                    'TH' => 'Thailand',
                    'TG' => 'Togo',
                    'TK' => 'Tokelau',
                    'TO' => 'Tonga',
                    'TT' => 'Trinidad And Tobago',
                    'TN' => 'Tunisia',
                    'TR' => 'Turkey',
                    'TM' => 'Turkmenistan',
                    'TC' => 'Turks And Caicos Islands',
                    'TV' => 'Tuvalu',
                    'UG' => 'Uganda',
                    'UA' => 'Ukraine',
                    'AE' => 'United Arab Emirates',
                    'GB' => 'United Kingdom',
                    'US' => 'United States',
                    'UM' => 'United States Minor Outlying Islands',
                    'UY' => 'Uruguay',
                    'UZ' => 'Uzbekistan',
                    'VU' => 'Vanuatu',
                    'VE' => 'Venezuela',
                    'VN' => 'Viet Nam',
                    'VG' => 'Virgin Islands (British)',
                    'VI' => 'Virgin Islands (U.S.)',
                    'WF' => 'Wallis And Futuna Islands',
                    'EH' => 'Western Sahara',
                    'YE' => 'Yemen',
                    'YU' => 'Yugoslavia',
                    'ZM' => 'Zambia',
                    'ZW' => 'Zimbabwe'
                    )
                ))
            ->add('address', 'textarea', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('company', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('job', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('allowsEmail', 'checkbox', array(
                'label' => 'Allows email',
                'attr' => array('style' => 'margin-left: 5px'),
                'required' => false
                ))
            ->add('locale', 'choice', array(
                'label' => 'Language',
                'attr' => array('class' => 'form-control'),
                'choices' => array('es' => 'Español', 'en' => 'English'),
                ))
        ;
    }
    
	/**
	 * Edit user profile uses a different validation 
	 */
    public function setDefaultOptions(OptionsResolverInterface $resolve)
    {
        $resolve->setDefaults(array(
            'data_class' => 'ROV\UsersBundle\Entity\User',
            'validation_groups' => array('Default')
        ));
    }
}