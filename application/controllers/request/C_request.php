<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_request extends CI_Controller {
    
    public function __construct() {

        parent::__construct();

        $this->load->model('request/M_request', 'request_model');
        define('KEY', 'AIzaSyBpuiyEEWl_F1ZfDUnSF6nvU2reGMdot7Q');
    }


    public function index() {
        
    
        $this->request_model->create_material_request();

        $pending_requests = $this->request_model->get_pending_request();
        $completed_requests = $this->request_model->get_completed_request();

        $data['pending_requests'] = $pending_requests;
        $data['completed_requests'] = $completed_requests;

        
        


        $this->load->view('pages/templates/header');
        $this->load->view('pages/request/v_index', $data);
        $this->load->view('pages/templates/footer'); 
       
    }

    
   
    public function view_request($order_id) {

        $data['request'] = $this->request_model->get_request_by_id($order_id);



        $this->load->view('pages/templates/header');
        $this->load->view('pages/request/v_index_request', $data);
        $this->load->view('pages/templates/footer');
    }


    public function complete_request($order_id) {

        $this->request_model->complete_request($order_id);

        redirect('/request/C_request/index');
    }

    public function pend_request($order_id) {

        $this->request_model->pend_request($order_id);

        redirect('/request/C_request/index');
    }

    public function get_request_sheet($error) {

        $request['sku'] = $this->input->post('sku');

        $request['good'] = $this->input->post('good');

        $request['order'] = $this->input->post('order');

        $request['comments'] = $this->input->post('comments');

        $request['description'] = $this->input->post('description');

        $request['warehouse'] = $this->input->post('warehouse');

        $request['quantity'] = $this->input->post('qty');

        $request['order_id'] = $this->request_model->generate_order_id();

        $this->request_model->insert_request($request);
        $this->request_model->stringify_request($request);


            
            $data['user'] = $this->session->all_userdata();

            $data['categories'] = $this->request_model->get_categories();

            $data['js'] = ['js/request/request.js'];

            $error = (isset($_GET['error'])) ? $_GET['error'] : null;

            $data['error'] = $error;

            $wh = (isset($_GET['wh'])) ? $_GET['wh'] : null;

            $warehouse = null;

            if (!is_null($wh)) {
                $warehouse = $this->request_model->get_warehouse_by_name($wh);
            }

            $data['warehouse'] = $warehouse;
            
            $data['products'] = $this->request_model->get_products_by_cat($warehouse['warehouse_code']);
    
            $this->load->view('pages/templates/header');
            $this->load->view('pages/request/v_request_sheet', $data);
            $this->load->view('pages/templates/footer');
    }

    public function add_product() {
            $this->load->helper(array('form', 'url'));

            $this->load->library('form_validation');


            $this->form_validation->set_rules('product_sku', 'Product Sku', 'required');
            $this->form_validation->set_rules('product_warehouse', 'Product Warehouse', 'required');
            $this->form_validation->set_rules('product_category', 'Product Category', 'required');

            $product['sku'] = $this->input->post('product_sku');
            $product['warehouse'] = $this->input->post('product_warehouse');
            $product['category'] = $this->input->post('product_category');
            $product['this_warehouse'] = $this->input->post('this_wh');

            $product['category'] = strtolower($product['category']);
            $product['warehouse'] = strtolower($product['warehouse']);

            if ($this->form_validation->run() == FALSE)
                {       
                        print('please fill out all fields');
                }

            else
                {
                    $this->request_model->add_product($product);
                }
            


    
    }

    public function delete_product($product_sku) {

        $this->request_model->delete_product($product_sku);

        redirect('/request/C_request/index');
    }

}
