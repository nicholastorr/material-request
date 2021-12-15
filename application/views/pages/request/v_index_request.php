


<h1>Warehouse: <?= $request[0]['warehouse'] ?></h1>
<?php if ($request[0]['status'] == 0) { ?>
    <h2>PENDING</h2>
<?php } else { ?>
    <h2>COMPLETED</h2>
<?php } ?>

<div class="new-form">
    <div class="form-headings">
        <h2>Sku</h2>
        <h2>Good</h2>
        <h2>Order</h2>
        <h2>Comments</h2>
    </div>
        <?php foreach ($request as $item) { ?>
            <div class="form">
                    <h3 class="request-line"><?= $item['sku'] ?></h3>
                    <h3 class="request-line"><?= $item['good'] ?></h3>
                    <h3 class="request-line"><?= $item['order'] ?></h3>
                    <h3 class="request-line"><?= $item['comments'] ?></h3>
            </div>
        <?php } ?>
</div>



<!--Complete button will set status to 1 and date completed to current date, reverts to pending if status is completed --->
<?php if ($request[0]['status'] == 0) { ?>
    <a href='/request/C_request/complete_request/<?php echo($request[0]['order_id']);?>'><button class="btn btn-primary btn-lg">Complete</button></a>
<?php } else { ?>
    <a href='/request/C_request/pend_request/<?php echo($request[0]['order_id']);?>'><button class="btn btn-primary btn-lg">Set to pending</button></a>
<?php } ?>

<button class="btn btn-primary btn-lg" onclick="window.location.href='/request/C_request/index'">Back</button>



<style>
    .request-line {
        width: 320px;
        text-decoration: none;
        border: 1px solid black;
        padding: 10px 20px 10px 2px;
        background-color: white;
        color: black;
        margin-bottom: -20px;
    }

    .form {
        display: flex;
        flex-direction: row;
        margin: auto;
    }

    span {
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
        margin-bottom: -30px;
    }

    .form-headings h2 {
        width: 320px;
        background-color: white;
        border: 1px solid black;
        padding: 0 50px 0 50px;
    }
</style>