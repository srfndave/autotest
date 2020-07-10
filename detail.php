<?php
session_start();
if(session_status() !== PHP_SESSION_ACTIVE or !isset($_SESSION["user"])) {
    header("Location: /login.php");
}
$title = "Vehicle Info";
?>
<?php
require_once 'header.php';
require_once 'dbinit.php';
require_once 'user.php';
require_once 'vehicle.php';

$conditions = array('All','New','Used');

if(isset($_GET["stock"])) {
    $stock = $_GET["stock"];
} else {
    $error_msg = "Please select a vehicle.";
}

$sql = 'SELECT * FROM vehicle WHERE stock = ?';
$sth = $pdo->prepare($sql);
$ret = $sth->execute([$stock]);
$vehicle = $sth->fetchObject('Vehicle');
?>
<div class="container">
    <?php include "topnav.php"; ?>
    <div class="row"><h1 class="col-sm-12 font-weight-bold text-secondary text-center"><?php echo $vehicle->name; ?></h1></div>
    <div class="row">
        <div class="col-sm-9">
            <a href="index.php">Return to list</a><br/>
            <img src="data:image/jpeg;base64,<?php echo base64_encode( $vehicle->photo )?>" alt="<?php echo $vehicle->name; ?>" />
            <div>
                <table class="table table-striped">
                    <thead>
                        <th>Options & Features</th>
                    </thead>
                    <tbody>
                <?php
                $options =  preg_split("/\r\n|\n|\r/", $vehicle->options);
                foreach($options as $option) {
                    echo "<tr><td>$option</td></tr>";
                }?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="font-weight-bold text-dark text-center">Pricing Details</h2>
                </div>
            </div>
            <?php if($vehicle->savings > 0) { ?>
                <div class="row py-2">
                    <div class="col-sm-6">Retail Price: </div>
                    <div class="col-sm-6 text-right">$<?php echo number_format($vehicle->retail,2); ?></div>
                </div>
                <div class="row py-2">
                    <div class="col-sm-6">Savings: </div>
                    <div class="col-sm-6 text-right">$<?php echo number_format($vehicle->savings,2); ?></div>
                </div>
            <?php } ?>
            <div class="row py-2">
                <div class="col-sm-6">Sale Price: </div>
                <div class="col-sm-6 text-right">$<?php echo number_format($vehicle->sales,2); ?></div>
            </div>
            <p></p>
            <div class="row" py-3>
                <div class="col-sm-3 text-right"><span class="fa fa-tachometer fa-3x text-secondary" /></div>
                <div class="col-sm-9 small text-dark"><?php echo nl2br($vehicle->data); ?></div>
            </div>
            <div class="row py-3">
                <div class="col-sm-3 text-right"><span class="fa fa-palette fa-3x text-secondary" /></div>
                <div class="col-sm-9 small text-dark">Exterior Color: <?php echo "$vehicle->color_exterior"; ?><br/>
                Interior Color: <?php echo "$vehicle->color_interior"; ?></div>
            </div>
            <div class="row py-3">
                <div class="col-sm-3 text-right"><span class="fa fa-clipboard-list fa-3x text-secondary text-right" /></div>
                <div class="col-sm-9 small text-dark">
                    Condition: <?php echo $conditions[$vehicle->condition_nu]; ?><br/>
                    Trim: <?php echo "$vehicle->trim"; ?><br/>
                    Mileage: <?php echo number_format($vehicle->mileage); ?><br/>
                    Stock #: <?php echo "$vehicle->stock"; ?><br/>
                    VIN: <?php echo "$vehicle->vin"; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php' ?>

