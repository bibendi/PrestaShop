<?php
/*
 * Prestashop test for eCommerce Manager app
 * KIS Software - www.kis-ecommerce.com
 * Version: 3.0
 */

$time = 0;

echo '<br/> * * * TEST definitions * * * <br/>';

echo '<b>1/4 AES key definition... </b>'; if (!check_aes_key_definition()) { echo 'OK'; } echo '<br/>';
echo '<b>2/4 language definition... </b>'; if (!check_main_language_definition()) { echo 'OK'; } echo '<br/>';
echo '<b>3/4 currency definition... </b>'; if (!check_main_currency_definition()) { echo 'OK'; } echo '<br/>';
echo '<b>4/4 employee definition... </b>'; if (!check_main_employee_definition()) { echo 'OK'; } echo '<br/>';

echo '<br/> * * * TEST queries * * * <br/>';

echo '<b>1/21 print notifications... </b>'; print_notifications($time . '*' . $time . '*' . $time); echo '<br/>';
echo '<b>2/21 print orders meta... </b>'; print_orders_meta($time, '0;#10'); echo '<br/>';
echo '<b>3/21 print orders... </b>'; print_orders($time, '0;#10'); echo '<br/>';
echo '<b>4/21 print order ids... </b>'; print_order_ids($time . '*' . 'test'); echo '<br/>';

echo '<b>5/21 print products meta... </b>'; print_products_meta($time, '0;#10'); echo '<br/>';
echo '<b>6/21 print products... </b>'; print_products($time, '0;#10'); echo '<br/>';
echo '<b>7/21 print product ids... </b>'; print_product_ids($time . '*' . 'test'); echo '<br/>';

echo '<b>8/21 print customers meta... </b>'; print_customers_meta($time, '0;#10'); echo '<br/>';
echo '<b>9/21 print customers... </b>'; print_customers($time, '0;#10'); echo '<br/>';
echo '<b>10/21 print customer ids... </b>'; print_customer_ids($time . '*' . 'test'); echo '<br/>';
echo '<b>11/21 print online customers... </b>'; print_online_customers(); echo '<br/>';
echo '<b>12/21 print categories... </b>'; print_categories(); echo '<br/>';
echo '<b>13/21 print order statuses... </b>'; print_order_statuses(); echo '<br/>';

echo '<b>14/21 print orders year statistics... </b>'; print_orders_year_statistics(); echo '<br/>';
echo '<b>15/21 print orders month statistics... </b>'; print_orders_month_statistics(date("Y")); echo '<br/>';
echo '<b>16/21 print orders day statistics... </b>'; print_orders_day_statistics(date("Y") . '*' . date("m")); echo '<br/>';
echo '<b>17/21 print orders hour statistics... </b>'; print_orders_hour_statistics(date("Y") . '*' . date("m") . '*' . date("d")); echo '<br/>';

echo '<b>18/21 print customers year statistics... </b>'; print_customers_year_statistics(); echo '<br/>';
echo '<b>19/21 print customers statistics... </b>'; print_customers_month_statistics(date("Y")); echo '<br/>';
echo '<b>20/21 print customers statistics... </b>'; print_customers_day_statistics(date("Y") . '*' . date("m")); echo '<br/>';
echo '<b>21/21 print customers statistics... </b>'; print_customers_hour_statistics(date("Y") . '*' . date("m") . '*' . date("d")); echo '<br/>';

echo '* * * TEST Finished! * * * <br/><br/>';
