<link rel="stylesheet" href="../../../../css/request/request_sheet.css"> 


<h1>Warehouse: <?= $request[0]['warehouse'] ?></h1>
<!-- display status of request -->
<?php if ($request[0]['status'] == 0) { ?>
    <h2>PENDING</h2>
<?php } else { ?>
    <h2>COMPLETED</h2>
<?php } ?>




<div class="new-form">
    <div class="form-headings">
        <h2>Sku</h2>
        <h2>Quantity</h2>
        <h2>Order</h2>
        <h2>Comments</h2>
    </div>
        <?php foreach ($request as $item) { ?>
            <div class="form">
                    <h3 class="request-line"><?= $item['sku'] ?></h3>
                    <h3 class="request-line"><?= $item['qty'] ?></h3>
                    <h3 class="request-line"><?= $item['order'] ?></h3>
                    <h3 class="request-line"><?= $item['comments'] ?></h3>
            </div>
        <?php } ?>
</div>



<!--Complete button will set status to 1 and date completed to current date, reverts to pending if status is completed --->
<div>
    <?php if ($request[0]['status'] == 0) { ?>
        <a href='/request/C_request/complete_request/<?php echo($request[0]['order_id']);?>'><button class="btn btn-primary btn-lg">Complete</button></a>
    <?php } else { ?>
        <a href='/request/C_request/pend_request/<?php echo($request[0]['order_id']);?>'><button class="btn btn-primary btn-lg">Set to pending</button></a>
    <?php } ?>

    <button class="btn btn-primary btn-lg" onclick="window.location.href='/request/C_request/index'">Back</button>
</div>


