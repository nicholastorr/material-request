<h1 style="margin-bottom: 75px;">Material Request</h1>



<div class="container">
    <div class="warehouses">
        <h3>Create new</h3>
            <?php if ($user['warehouse'] == '1') { ?>
                <a class="wh" href="/request/C_request/get_request_sheet/box?wh=tampa">Tampa</a>
            <?php } ?>
            <?php if ($user['warehouse'] == '15') { ?>
                <a class="wh" href="/request/C_request/get_request_sheet/box?wh=chicago">Chicago</a>
            <?php } ?>
            <?php if ($user['warehouse'] == '11') { ?>
                <a class="wh" href="/request/C_request/get_request_sheet/box?wh=vegas">Las Vegas</a>
            <?php } ?>
            <?php if ($user['warehouse'] == '30') { ?>
                <a class="wh" href="/request/C_request/get_request_sheet/box?wh=denver">Denver</a>
            <?php } ?>   
     
                
   
            
    </div>

    <div class="pending-requests">
        <h3>Pending Requests</h3>
        <!-- for each loop to display pending requests -->
            <?php foreach ($pending_requests as $row) { ?>
                <div class="pendings">
                    <a class="request" href="<?php echo '/request/c_request/view_request/' . $row['order_id'] ?>"><p><?php echo $row['order_id']; ?></p><p><?php echo ucfirst($row['warehouse']); ?></p><p><?php echo explode(" ", $row['date_created'])[0]; ?></p></a>
                </div>
            <?php } ?>
    </div>

    <div class="completed-requests">
        <h3>Completed Requests</h3>
        <!-- for each loop to display completed requests -->
            <?php foreach ($completed_requests as $row) { ?>
                <div class="pendings">
                    <a class="request" href="<?php echo '/request/c_request/view_request/' . $row['order_id'] ?>"><p><?php echo $row['order_id']; ?></p><p><?php echo ucfirst($row['warehouse']); ?></p><p><?php echo explode(" ", $row['date_created'])[0]; ?></p></a>
                </div>
            <?php } ?>
    </div>
</div>





<style>

    .container {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .content {
        margin: 0;
    }

    .request {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .wh, .request {
        text-decoration: none;
        border: 1px solid black;
        padding: 10px 20px 10px 2px;
        background-color: silver;
        margin-bottom: 20px;
        color: black;
    }

    .warehouses, .pending-requests, .completed-requests {
        width: 30%;
        position: left;
        display: flex;
        flex-direction: column;
        font-size: 25px;
    }
</style>