<link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto">


<h1 id="warehouse"><?php echo ($warehouse['warehouse_name']) ?></h1>
    <?php if ($error) { ?>
        <h1 style="color: red"><?php print($error) ?></h1>
    <?php } ?>
<h1 style="margin-bottom: 75px">New Material Request Form</h1>


<div class="body">
    <div class="categories">
        <h2>Categories</h2>
        <!---on click, pass data to model--->
        <?php foreach ($categories as $category): ?>
            <li id="<?php echo str_replace(' ', '-', $category['category']) . 1 ?>" style="font-size: 30px;"><?php echo ucfirst($category['category']) ?>
            <a style="margin-left: 20px; height: 15px;" href="/request/C_request/delete_category/<?php echo $category['category'] ?>"><i style="height: 15px; color: red;" class="fas fa-trash-alt"></i></a></li>
        <?php endforeach; ?>

        <?php if (in_array('Administrator', $user['roles'])) { ?>
            <h3 id="add_product_button"><span class="glyphicon glyphicon-plus"></span>Add Product</h3>
        <?php
            $attrs = array('name' => 'new_product', 'class' => 'new_product', 'style' => 'display: none');
            echo form_open_multipart('request/C_request/add_product' , $attrs);
        ?>
            <input type="text" name="product_sku" placeholder="Product SKU" required />
            <input type="text" name="product_warehouse" placeholder="tampa/denver/vegas/chicago" required />
            <select id="categories" name="product_category" placeholder="Product Category">
                <?php foreach ($categories as $category): ?>
                    <option style="font-size: 20px;" value="<?php echo $category['category'] ?>"><?php echo $category['category'] ?></option>
                <?php endforeach; ?>
            </select>
                <span style="display: none"><input type="textarea" id="this_wh" name="this_wh" value="<?= $warehouse['warehouse_code'] ?>"  style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></input></span>
        </form>


            <h3 id="add_category_button"><span class="glyphicon glyphicon-plus"></span>Add Category</h3>
            <?php
                $attrs = array('name' => 'new_category', 'class' => 'new_category', 'style' => 'display: none');
                echo form_open_multipart('request/C_request/add_category' , $attrs);
            ?>
                <input type="text" name="category" placeholder="Category" required />
                <span style="display: none"><input type="textarea" id="this_wh" name="this_wh1" value="<?= $warehouse['warehouse_code'] ?>"  style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></input></span>
            </form>
        <?php } ?>
</div>

            <button type="button" style="display: none" class="btn btn-primary btn-lg"  onclick="add_product()" id="accept-count">submit</button>
            <button id="category-button" type="button" style="display: none" class="btn btn-primary btn-lg"  onclick="add_category()" id="accept-count">submit</button>
        <?php echo form_close(); ?>

        <?php
            $attributes = array('name' => 'count_form');
            echo form_open_multipart('request/C_request/get_request_sheet/box?wh=' .  strtolower($warehouse['warehouse_code']) , $attributes);
        ?>
        <div class="new-form">
                <div class="form-info">
                    <h2><span class="glyphicon glyphicon-asterisk"></span>Order, Comments, Qty Required -- Leave blank to omit SKU from request</h2>
                    <h2><span class="glyphicon glyphicon-asterisk"></span>To adjust position, leave fields blank and input weight (descending order)</h2>
                </div>
            <div class="form-headings">
            <h2>Sku</h2>
            <h2>Order</h2>
            <h2>Comments</h2>
            <h2 class="smaller-head">Qty</h2>
            <h2 class="smaller-head">Weight</h2>
            </div>
        <?php foreach($products as $product) { ?>
            <div id="form" class="<?php echo str_replace(' ', '-', $product['category']) ?>"  style="display: flex; flex-direction: column; display: none">
            <div class="form">
                <a href="/request/C_request/delete_product/<?= $product['sku'] ?>"><span class="glyphicon glyphicon-remove"></span></a>
                <span><input type="textarea" value="<?= $product['sku'] ?>" id="sku" name="sku[]" style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
                <span class="order"><input type="textarea" id="order" name="order[]" style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
                <span class="comments"><input type="textarea" id="comments" name="comments[]" style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
                <span><input type="textarea" id="good" name="qty[]" style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
                <span><input type="textarea" value="<?= $product['weight'] ?>" placeholder="<?= $product['weight'] ?>" id="good" class="weight" name="weight[]" style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></span>
            </div>
            </div>
                <?php echo '<script>var myPhpVariable = "'. str_replace(' ', '-', $product['category']) . '";</script>'; ?>
        <?php }; ?>
        </div>

        <div style="display: none"><input type="textarea" id="warehouse" name="warehouse[]" value="<?= $warehouse['warehouse_code'] ?>"  style="font-size: 30px; background-color: #DCDCDC; height: 80%; margin-right: 15px"></input></div>
        <button type="button" class="btn btn-primary btn-lg"  onclick="myFunc()" id="count">submit</button>

        <div id="boxes1"></div>

        <?php echo form_close(); ?>
</div>






<script>
    function myFunc() {
        document.count_form.submit();

    };

    function add_product() {
        document.new_product.submit();
    };

    function add_category() {
        document.new_category.submit();
    };

    function onlyUnique(value, index, self) {
        return self.indexOf(value) === index;
    }


    $('#count').each(function() {
        $('#count').click(function () {
            document.count_form.submit();
        });
    });
    
            var newArr = [];

            $('#add_product_button').click(function() {
                $('.new_product').toggle();
                $('#accept-count').toggle();
                $('.new-form').toggle();
                $('#count').toggle();
            });

            $('#add_category_button').click(function() {
                $('.new_category').toggle();
                $('#category-button').toggle();
                $('.new-form').toggle();
                $('#count').toggle();
            });

        
            var arrayFromPHP = <?php echo json_encode($products); ?>;
                $.each(arrayFromPHP, function (i, elem) {
                    newArr.push(elem.category.replace(" ", "-"));
                });

            var unique = newArr.filter(onlyUnique);

            console.log(unique[0]);


            $.each(unique, function (i, elem) {
                $('#' + elem + 1).click(function() {
                    $("." + elem).toggle();
                });
            });

   
            
   
    
</script>







<style>
    .glyphicon.glyphicon-remove {
        color: red;
        font-size: 30px;
    }

    .glyphicon.glyphicon-asterisk {
        font-size: 20px;
    }

    .body {
        display: flex;
        flex-direction: row;
    }

    div.box {
        background: none;
        border: none;
        box-shadow: none;
        margin-bottom: none;
    }

    .box {
        margin-bottom: none;
    }

    #text {
        display: none !important;
    }

    #form {
        margin-left: auto;
        margin-right: auto;
    }

    .form-headings {
        display: flex;
        text-align: center;
        flex-direction: row;
        margin-left: auto;
        margin-right: auto;
    }

    .form-headings h2 {
        width: 300px;
        background-color: white;
        border: 1px solid black;
        padding: 0 50px 0 50px;
    }

    .smaller-head {
        width: 100px !important;
        background-color: white;
        border: 1px solid black;
        padding: 0 !important;
    }

    .new-form {
        display: flex;
        flex-direction: column;
    }

    input#good {
        background-color: white !important;
        width: 100px !important;
        margin: 0 !important;
    }

    input#sku, input#order, input#comments {
        background-color: white !important;
        width: 290px !important;
        margin: 0 !important;
    }

    li {
        margin-bottom: 20px;
        list-style-type: disclosure-open;

    }

    .new_product input {
        width: 320px;
        height: 40px;
    }

    .categories {
        font-family: 'Roboto', sans-serif;
        background-color: white;
        border: 1px solid black;
        padding: 0 50px 0 50px;
        margin-right: 20px;
    }

    #categories {
        height: 40px;
    }

    #count {
        margin-top: 30px;
    }

    .form-info {
        border: 1px solid black;
        background-color: white;
        padding: 0 50px 0 50px;
        color: red;
    }
</style>
