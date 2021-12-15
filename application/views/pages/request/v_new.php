

<script src="../../js/request/request.js"></script>






<h1 onblur="sumFunction()" id="warehouse"><?= $warehouse['warehouse_name'] ?></h1>

<h1 style="margin-bottom: 75px">New Material Request Form</h1>




   
    <?php
                $attributes = array('name' => 'count_form');
                echo form_open_multipart('request/C_request/new_material_request?wh=' .  strtolower($warehouse['warehouse_code']) , $attributes);
                ?>

<div>
<h2>Categories</h2>
    <!---on click, pass data to model--->
    <a href='/request/C_request/get_request_sheet/box?wh=<?php echo strtolower($warehouse['warehouse_code']) ?>'><li style="font-size: 20px;">Boxes</li></a>
    <a href='/request/C_request/get_request_sheet/labels?wh=<?php echo strtolower($warehouse['warehouse_code']) ?>'><li style="font-size: 20px;">Labels</li></a>
</div>

<div class="new-form">
    <div class="form-headings">
        <h2>Sku</h2>
        <h2>Description</h2>
        <h2>Quantity</h2>
    </div>
    <div id="rows" style="display: flex; flex-direction: column;">
        <div class="form">
            <span><input type="textarea" id="sku" name="sku[]" onblur="sumFunction()"  style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
            <span><input type="textarea" id="description" name="description[]" onblur="sumFunction()"  style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
            <span class="qty"><input type="textarea" id="qty" name="qty[]" onblur="sumFunction()"  style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
        </div>
    </div>
        <span style="display: none"><input type="textarea" id="order_id" name="order_id[]" onblur="sumFunction()" value="<?= $order_id ?>"  style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></input></span>
        <span style="display: none"><input type="textarea" id="warehouse" name="warehouse[]" onblur="sumFunction()" value="<?= $warehouse['warehouse_name'] ?>"  style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
</div>




<div id="add-row" class="btn btn-primary btn-lg">add row</div>
<button type="button" class="btn btn-primary btn-lg"  onclick="submitForm()" id="accept-count" style="">submit</button>




<style>
    .form {
        display: flex;
        flex-direction: row;
        margin: auto;
       margin-bottom: -10px;
    }
    
    .qty {


    }

    input {

    }

    .new-form {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .form-headings {
        display: flex;
        text-align: center;
        flex-direction: row;
        align-items: center;
        margin: auto;
    }

    .form-headings h2 {
        width: 320px;
        background-color: white;
        border: 1px solid black;
        padding: 0 50px 0 50px;
    }

    input#description, input#sku, input#qty {
        background-color: white !important;
        width: 320px !important;
        margin: 0 !important;
    }
</style>


