<?php

class Admin1cRetailSalesReportControllerCore extends AdminController
{
    public function initContent()
    {
        $this->display = 'edit';
        $this->initToolbar();
        $this->content .= $this->initFormByDate();
        $this->table = 'invoice';

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
        ));
    }

    public function initToolbar()
    {
        $this->toolbar_btn['save-date'] = array(
            'href' => '#',
            'desc' => $this->l('Generate retail sales report file by date')
        );
    }

    public function initFormByDate()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('By date'),
                'image' => '../img/admin/export.gif'
            ),
            'input' => array(
                array(
                    'type' => 'date',
                    'label' => $this->l('Date:'),
                    'name' => 'date',
                    'size' => 20,
                    'maxlength' => 10,
                    'required' => true,
                    'desc' => $this->l('Format: 2011-12-31 (inclusive)')
                )/*,
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Statuses:'),
                    'name' => 'id_order_state',
                    'values' => array(
                        'query' => OrderState::getOrderStates($this->context->language->id),
                        'id' => 'id_order_state',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('You can also export orders which have not been charged yet.').' (<img src="../img/admin/charged_ko.gif" alt="" />).'
                )*/
            ),
            'submit' => array(
                'title' => $this->l('Generate report'),
                'class' => 'button',
                'id' => 'submitPrint'
            )
        );

        $this->fields_value = array(
            'date_from' => date('Y-m-d')
        );

        $this->table = 'report_date';
        $this->toolbar_title = $this->l('Generate report');
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::getValue('submitAddreport_date'))
        {
            if (!Validate::isDate(Tools::getValue('date')))
                $this->errors[] = $this->l('Invalid date');

            if (!count($this->errors))
            {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    select
                      coalesce(pa.reference, p.reference) as reference,
                      od.product_quantity,
                      truncate(od.unit_price_tax_excl  * (1 + ((o.total_paid - o.total_products) / o.total_products)), 2) as unit_price,
                      truncate(od.total_price_tax_excl * (1 + ((o.total_paid - o.total_products) / o.total_products)), 2) as total_price
                    from '._DB_PREFIX_.'order_history as oh
                    inner join '._DB_PREFIX_.'orders as o on o.id_order = oh.id_order
                    inner join '._DB_PREFIX_.'order_detail as od on od.id_order = o.id_order
                    inner join '._DB_PREFIX_.'product as p on p.id_product = od.product_id
                    inner join '._DB_PREFIX_.'product_lang as pl on pl.id_product = p.id_product and pl.id_lang = 1
                    left join '._DB_PREFIX_.'product_attribute as pa on pa.id_product_attribute = od.product_attribute_id
                    where
                      oh.date_add >= "'.Tools::getValue('date').' 00:00:00"
                      and oh.date_add <= "'.Tools::getValue('date').' 23:59:59"
                      and oh.id_order_state = 14');

                $rows = array();
                $row = array();
                $row[] = 'Артикул';
                $row[] = 'Количество';
                $row[] = 'Цена';
                $row[] = 'Сумма';
                $row[] = 'Ставка НДС';
                $row[] = 'Сумма НДС';
                $row[] = 'Собственные, счет учета';
                $row[] = 'Счет учета НДС по реализации';
                $row[] = 'Счет доходов';
                $row[] = 'Субконто';
                $row[] = 'Счет расходов';
                $rows[] = implode("\t", $row);

                foreach ($result as $raw_row) {
                    $row = array();
                    $row[] = $raw_row['reference'];
                    $row[] = $raw_row['product_quantity'];
                    $row[] = $raw_row['unit_price'];
                    $row[] = $raw_row['total_price'];
                    $row[] = 'Без НДС';
                    $row[] = '';
                    $row[] = '41.01';
                    $row[] = '90.03';
                    $row[] = '90.01.1';
                    $row[] = '';
                    $row[] = '90.02.1';
                    $rows[] = implode("\t", $row);
                }

                header('Content-Description: File Transfer');
                header('Content-Type: text/plain');
                header('Content-Disposition: attachment; filename=sales-report.txt');
                echo iconv('utf-8', 'windows-1251', implode("\n", $rows));
                exit;
            }
        }
    }
}
?>