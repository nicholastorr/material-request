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


    //take in warehouse id and return warehouse name
	public function get_warehouse_by_name($name) {

        $data['tampa'] = ['warehouse_code' => 'tampa', 'warehouse_name' => 'Tampa', 'warehouse_id' => '1'];
        $data['vegas'] = ['warehouse_code' => 'vegas', 'warehouse_name' => 'Las Vegas', 'warehouse_id' => '11'];
        $data['chicago'] = ['warehouse_code' => 'chicago', 'warehouse_name' => 'Chicago', 'warehouse_id' => '15'];
        $data['denver'] = ['warehouse_code' => 'denver', 'warehouse_name' => 'Denver', 'warehouse_id' => '30'];

        return (isset($data[$name])) ? $data[$name] : null;
    }

    public function insert_request($request) {
              
       
        //warehouse name and order_id only need to be input once for the whole request so they are set as "constants"
        $warehouse = $request['warehouse'][0];
        $order_id = $request['order_id'];
        
        //sku is only thing required, requests are sent as array of array so loop through and create one request for each product line
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
                    'date_created' => date('m-d-Y H:i:s'),
                    'qty' => $request['quantity'][$key],
                    'order_id' => $order_id,
                    'order' => $request['order'][$key],
                    'comments' => $request['comments'][$key]
                );
                $this->db_op->insert('material_request_lines', $data);
            }
        //$this->db_op->query("insert into material_request_lines (sku, description, status, warehouse, date_created, order_id) values ('" . $request['sku'] . "', '" . $request['description'] . "',   0, '" . $request['warehouse'] . "', CURRENT_TIMESTAMP, '" . $request['order_id'] . "') ");
        
        }
    }


    //function to generate unique request id for each material request
    public function generate_order_id() {
        
        //get last id from material_request_lines table and add one to make unique order_id
        $query = $this->db_op->query("select max(order_id) as order_id from material_request_lines");
        $result = $query->result_array();
        return $result[0]['order_id'] + 1;
    }

    //on index page load, run function to make a request out of material request lines
    public function create_material_request() {
        $affected = 0;
        //first query for existing material request lines
        $query = $this->db_op->query("select distinct(order_id) order_id, warehouse, status, date_created, date_completed
                                    from material_request_lines")->result_array();
        //second query for material requests
        $query2 = $this->db_op->query("SELECT distinct(order_id) from material_request")->result_array();

        //create temp array to use for check if material request exists loop
        $temp = array();
        foreach ($query2 as $key => $value) {
            $temp[] = $value['order_id'];
        }
        //loop through material request lines and create material request if it doesn't exist
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

    //get requests where status = 0
    public function get_pending_request() {

        $query = $this->db_op->query("select * from material_request where status = 0");

        return $query->result_array();
    }

    //get requests where status = 1
    public function get_completed_request() {
        $query = $this->db_op->query("select * from material_request where status = 1");

        return $query->result_array();
    }

    //get request by unique order_id
    public function get_request_by_id($order_id) {
        $query = $this->db_op->query("select * from material_request_lines where order_id = '" . $order_id . "'");

        return $query->result_array();
    }

    //set requests of material_requests and material_request_lines to status = 1
    public function complete_request($order_id) {
        $this->db_op->query("update material_request_lines set status = 1, date_completed = CURRENT_TIMESTAMP where order_id = '" . $order_id . "'");
        $this->db_op->query("update material_request set status = 1, date_completed = CURRENT_TIMESTAMP where order_id = '" . $order_id . "'");
    }

    //set requests of material_requests and material_request_lines to status = 0
    public function pend_request($order_id) {
        $this->db_op->query("update material_request_lines set status = 0, date_completed = null where order_id = '" . $order_id . "'");
        $this->db_op->query("update material_request set status = 0, date_completed = null where order_id = '" . $order_id . "'");
    }

    //onclick of product category in sheet view, get all products for that category
    public function get_products_by_cat($warehouse) {
        $query = $this->db_op->query("select * from material_request_products where warehouse like '%" . $warehouse . "%' order by weight desc");

        return $query->result_array();
    }

    //fetches all categories and sends to view through get_request_sheet controllers
    public function get_categories() {
        $query = $this->db_op->query("select distinct(category) from material_request_products");

        return $query->result_array();
    }

    //add product to material req if it doesn't exist
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

            redirect('request/C_request/get_request_sheet/box?wh=' .  strtolower($product['this_warehouse']) . '&sku=' . $product['sku']);
        }
        else {
            $error = 'Product already exists';
            redirect('request/C_request/get_request_sheet/box?wh=' .  strtolower($product['this_warehouse']) . '&error=' . $error);
        }
    }

    public function delete_product($product_sku) {
        $this->db_op->query("delete from material_request_products where sku = '" . $product_sku . "'");
    }

    //on request submit turn request into an html table and email to purchasing
    public function stringify_request($request) {
        $warehouse = $request['warehouse'][0];
        $order_id = $request['order_id'];
        $orderArray = [];
    
        if ($request['sku'] != null) {
            foreach ($request['sku'] as $key => $sku) {
                if ($request['order'][$key] == "") {
                    continue;
                }
                //take request and make into array for switching into html table
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
                    array_push($orderArray, $data);
                }
        }       
                $output = "";
                //foreach loop for each line of material_requests
                foreach ($orderArray as $key => $value) {
                    $output .= "<tr>";
                    $output .= "<td>" .  "SKU: " . $value['sku'] . " || " . "</td>";
                    $output .= "<td>" . "Warehouse: " . $value['warehouse'] . " || " . "</td>";
                    $output .= "<td>" . "Date Created: " . $value['date_created'] . " || " . "</td>";
                    $output .= "<td>" . "Order Id: " . $value['order_id'] . " || " . "</td>";
                    $output .= "<td>" . "Quantity: " . $value['qty'] . " || " . "</td>";
                    $output .= "<td>" . "Order: " . $value['order'] . " || " . "</td>";
                    $output .= "<td>" . "Comments: " . $value['comments'] . "</td>";
                    $output .= "</tr>";
                
                    $output .= "<style>" . "td {border: 1px solid black; background-color: red;}" . "</style>";
                }
                $this->load->library('email');

                //smtp config settings
                $config = array();
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = 'ssl://smtp.gmail.com';
                $config['smtp_user'] = 'sales@mbs-standoffs.com';
                $config['smtp_pass'] = '@HtmMbs1206@';
                $config['smtp_port'] = 465;
                $config['mailtype'] = 'html';
                $config['validation'] = TRUE;

                $this->email->initialize($config);
                $this->email->from('sales@mbs-standoffs.com', 'Material Request');
                $this->email->to('web3@htm-mbs.com');
                $this->email->subject('Material Request');
                $this->email->message($output);
        
                if ($orderArray != null) {
                    $this->email->send();
                    redirect('/request/C_request/index');
                }
    }

    //creates function and displays on request sheet on click
    public function add_category($category) {
        $query = $this->db_op->query("select * from material_request_products where category = '" . $category['category'] . "'")->result_array();

        //if product doesn't exists then run
        if (count($query) == 0) {
            $data = array(
                'sku' => null,
                'warehouse' => null,
                'category' => $category['category'],
            );
            $this->db_op->insert('material_request_products', $data);

            redirect('request/C_request/get_request_sheet/box?wh=' .  strtolower($category['this_warehouse']));
        }
        else {
            $error = 'Category already exists';
            redirect('request/C_request/get_request_sheet/box?wh=' .  strtolower($category['this_warehouse']) . '&error=' . $error);
        }
    }

    public function delete_category($category) {
        $this->db_op->query("delete from material_request_products where category = '" . strtolower($category) . "'");
    }

    public function update_weight($request) {
        foreach ($request['sku'] as $key => $sku) {
            $this->db_op->query("update material_request_products set weight = " . (int)$request['weight'][$key] . "  where sku = '" . $request['sku'][$key]  . "'");
        }

    }

}