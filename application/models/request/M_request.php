<?php

class M_request extends CI_Model {

    public function __construct() {

        parent::__construct();


        ##################################################
        $_SESSION['customer_care']['server'] = 'odoo12';
        $_SESSION['customer_care']['state'] = 'production';

        $this->db_wt = $this->load->database('default', TRUE); //wt = warehouse test
        $this->db_op = $this->load->database('odoo_production', TRUE); // op = odoo production
    }

	public function get_warehouse_by_name($name) {

        $data['tampa'] = ['warehouse_code' => 'tampa', 'warehouse_name' => 'Tampa', 'warehouse_id' => '1'];
        $data['vegas'] = ['warehouse_code' => 'vegas', 'warehouse_name' => 'Las Vegas', 'warehouse_id' => '11'];
        $data['chicago'] = ['warehouse_code' => 'chicago', 'warehouse_name' => 'Chicago', 'warehouse_id' => '15'];
        $data['denver'] = ['warehouse_code' => 'denver', 'warehouse_name' => 'Denver', 'warehouse_id' => '30'];

        return (isset($data[$name])) ? $data[$name] : null;
    }

    public function insert_request($request) {
              
        
            $warehouse = $request['warehouse'][0];
            $order_id = $request['order_id'];
        
        if ($request['sku'] != null) {
            foreach ($request['sku'] as $key => $sku) {
                if ($request['order'][$key] == "") {
                    continue;
                }
                $data = array(
                    'sku' => $sku,
                    'description' => $request['description'][$key],
                    'warehouse' => $warehouse,
                    'status' => '0',
                    'date_created' => date('Y-m-d H:i:s'),
                    'order_id' => $order_id,
                    'qty' => $request['quantity'][$key],
                    'good' => $request['good'][$key],
                    'order' => $request['order'][$key],
                    'comments' => $request['comments'][$key]
                );
                $this->db_op->insert('material_request_lines', $data);
            }
        //$this->db_op->query("insert into material_request_lines (sku, description, status, warehouse, date_created, order_id) values ('" . $request['sku'] . "', '" . $request['description'] . "',   0, '" . $request['warehouse'] . "', CURRENT_TIMESTAMP, '" . $request['order_id'] . "') ");
        
        }
    }

    public function generate_order_id() {
            
        $query = $this->db_op->query("select max(order_id) as order_id from material_request_lines");
        $result = $query->result_array();
        return $result[0]['order_id'] + 1;
    }

    public function create_material_request() {
        $affected = 0;
        $query = $this->db_op->query("select distinct(order_id) order_id, warehouse, status, date_created, date_completed
                                    from material_request_lines")->result_array();

        $query2 = $this->db_op->query("SELECT distinct(order_id) from material_request")->result_array();

        $temp = array();
        foreach ($query2 as $key => $value) {
            $temp[] = $value['order_id'];
        }

        foreach ($query as  $value) {
            $exist = FALSE;
            foreach ($temp as $key => $row) {
                if ($value['order_id'] == $row) {
                    $exist = TRUE;
                    break;
                }
            }

            if (!$exist) {
                $data = array(
                    'warehouse' => $value['warehouse'],
                    'status' => $value['status'],
                    'date_created' => $value['date_created'],
                    'order_id' => $value['order_id'],
                    'date_completed' => $value['date_completed']
                );
                $this->db_op->insert('material_request', $data);
                $affected++;
            }
        }
    }


    public function get_pending_request() {

        $query = $this->db_op->query("select * from material_request where status = 0");

        return $query->result_array();
    }

    public function get_completed_request() {
        $query = $this->db_op->query("select * from material_request where status = 1");

        return $query->result_array();
    }


    public function get_request_by_id($order_id) {
        $query = $this->db_op->query("select * from material_request_lines where order_id = '" . $order_id . "'");

        return $query->result_array();
    }

    public function complete_request($order_id) {
        $this->db_op->query("update material_request_lines set status = 1, date_completed = CURRENT_TIMESTAMP where order_id = '" . $order_id . "'");
        $this->db_op->query("update material_request set status = 1, date_completed = CURRENT_TIMESTAMP where order_id = '" . $order_id . "'");
    }

    public function pend_request($order_id) {
        $this->db_op->query("update material_request_lines set status = 0, date_completed = null where order_id = '" . $order_id . "'");
        $this->db_op->query("update material_request set status = 0, date_completed = null where order_id = '" . $order_id . "'");
    }

    public function get_products_by_cat( $warehouse) {
        $query = $this->db_op->query("select * from material_request_products where warehouse like '%" . $warehouse . "%'");

        return $query->result_array();
    }

    public function get_categories() {
        $query = $this->db_op->query("select distinct(category) from material_request_products");

        return $query->result_array();
    }

    public function add_product($product) {

        $query = $this->db_op->query("select * from material_request_products where sku = '" . $product['sku'] . "'")->result_array();


        //if product doesn't exists then run
        if (count($query) == 0) {
            $data = array(
                'sku' => $product['sku'],
                'warehouse' => $product['warehouse'],
                'category' => $product['category'],
            );
            $this->db_op->insert('material_request_products', $data);

        redirect('request/C_request/get_request_sheet/box?wh=' .  strtolower($product['this_warehouse']));
        }
        else {
            $error = 'Product already exists';
            redirect('request/C_request/get_request_sheet/box?wh=' .  strtolower($product['this_warehouse']) . '&error=' . $error);
        }
    }

    public function delete_product($product_sku) {
        $this->db_op->query("delete from material_request_products where sku = '" . $product_sku . "'");
    }


    

    //send email that doesnt work
    public function sendEnquiry() {
        $this->load->library('email');


        $config = array();
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_user'] = 'sales@mbs-standoffs.com';
        $config['smtp_pass'] = '@HtmMbs1206@';
        $config['smtp_port'] = 465;
        $config['mailtype'] = 'html';
        $config['validation'] = TRUE;
        $this->email->initialize($config);

        $this->email->from('sales@mbs-standoffs.com', 'Identification');
        $this->email->to('web3@htm-mbs.com');
        $this->email->subject('Send Email Codeigniter');
        $this->email->message('The email send using codeigniter library');

        $this->email->send();
    }

}