<?php
session_start();
if(session_status() !== PHP_SESSION_ACTIVE or !isset($_SESSION["user"])) {
    header("Location: /login.php");
}
$title = "Dave Autos";
?>
<?php
require_once 'header.php';
?>
<script type="text/javascript">
function getModels(make) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var myArr = JSON.parse(this.responseText);
            //var mymodelcnt = myArr.feeds.length;
            //document.getElementById("feedheader").innerHTML = "Number of feeds: " + myfeedcnt;
            //document.getElementById("feedlupd" + feedidx).innerHTML = myArr.feeds[i].lastupdate;
            //var totalunread = 0;
            //for (var i = 0; i < myfeedcnt; i++) {
            //    var feedidx = myArr.feeds[i].idx;
           // }
        }
    };
    xmlhttp.open("GET", "ajax.php?req=Model&make=" + make, true);
    xmlhttp.send();
}
</script>
<?php
require_once 'dbinit.php';
require_once 'user.php';
require_once 'vehicle.php';

$conditions = array('All','New','Used');
$result_counts = array('10','20','30','40','50');

// build where clause based on selected filters
$condition_where = "where";

// type will be All, New or Used but database uses a 1 for New and 2 for Used
$type = isset($_SESSION["type"]) ? $_SESSION["type"] : "All";
if(isset($_POST["type"])) {
    $type = $_POST["type"];
    $_SESSION["type"] = $type;
}
if($type === 'New') {
    $condition_where .= " condition_nu = '1'";
} elseif($type === 'Used') {
    $condition_where .= " condition_nu = '2'";
}

// general search string, will search only on name in database for now
$search_str = isset($_SESSION["search_str"]) ? $_SESSION["search_str"] : "";
if(isset($_POST["search_str"])) {
    $search_str = strip_tags($_POST["search_str"]);
    $_SESSION["search_str"] = $search_str;
}
if(isset($search_str) and $search_str != "") {
    $condition_where .= " name like :search_str";
    $query_params['search_str'] = "%" . $search_str . "%";
}

// year search value, should be a four-digit number
$search_year = isset($_SESSION["search_year"]) ? $_SESSION["search_year"] : "";
if(isset($_POST["search_year"])) {
    $search_year = strip_tags($_POST["search_year"]);
    $_SESSION["search_year"] = $search_year;
}
if(isset($search_year) and $search_year != "") {
    if($search_year !== "All") {
        $and = ($condition_where === "where") ? "" : " and";
        $condition_where .= "$and year = :search_year";
        $query_params['search_year'] = "$search_year";
    }
}

// make search value, from database list
$search_make = isset($_SESSION["search_make"]) ? $_SESSION["search_make"] : "";
$make_changed = false;
if(isset($_POST["search_make"])) {
    $search_make = strip_tags($_POST["search_make"]);
    if($search_make != $_SESSION["search_make"]) {
        $_SESSION["search_make"] = $search_make;
        $search_model = "All";
        $_SESSION["search_model"] = $search_model;
        $make_changed = true;
    }
}
if(isset($search_make) and $search_make != "") {
    if($search_make !== "All") {
        $and = ($condition_where === "where") ? "" : " and";
        $condition_where .= "$and make = :search_make";
        $query_params['search_make'] = $search_make;
    }
}

// model search value, from database list
$search_model = isset($_SESSION["search_model"]) ? $_SESSION["search_model"] : "";
if(isset($_POST["search_model"]) and ! $make_changed) {
    $search_model = strip_tags($_POST["search_model"]);
    $_SESSION["search_model"] = $search_model;
}
if(isset($search_model) and $search_model != "") {
    if($search_model !== "All") {
        $and = ($condition_where === "where") ? "" : " and";
        $condition_where .= "$and model = :search_model";
        $query_params['search_model'] = $search_model;
    }
}

// if no filters were selected, then don't need a where condition
if($condition_where === "where") {
    $condition_where = "";
}

// get the page number selected and build the page selector based on count of vehicles matching search
$page = (isset($_SESSION["page"]) and $_SESSION["page"] > 0) ? $_SESSION["page"] : "1";
if(isset($_GET["page"]) && is_numeric($_GET["page"])) {
    $page = (int)$_GET["page"];
    if($page < 1) {
        $page = 1;
    }
}
$_SESSION["page"] = $page;
$results_per_page = isset($_SESSION["results_per_page"]) ? $_SESSION["results_per_page"] : "10";
if(isset($_POST["results_per_page"]) and in_array($_POST["results_per_page"],$result_counts)) {
    $results_per_page = $_POST["results_per_page"];
    $_SESSION["results_per_page"] = $results_per_page;
}

$sth = $pdo->prepare("select count(*) from vehicle $condition_where");
if($condition_where !== "" and isset($query_params) and count($query_params) > 0) {
    foreach (array_keys($query_params) as $param) {
        $bind_type = is_int($query_params[$param]) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $sth->bindParam($param, $query_params[$param], $bind_type);
    }
}
$ret = $sth->execute();
$match_count = $sth->fetchColumn();
if($match_count > 0) {
    // build array of pages
    $pages = array();
    $page_no = 1;
    while($page_no <= ceil($match_count / $results_per_page)) {
        array_push($pages, $page_no);
        $page_no++;
    }
    // make sure page number is less than or equal to the number of pages
    while(ceil($match_count / $results_per_page) < $page) {
        $page--;
        $_SESSION["page"] = $page;
    }
    $offset = ($page - 1) * $results_per_page; // offset for database LIMIT part of query
    if($offset < 0) {
        $offset = 0;
    }
    $query_params["offset"] = (int)$offset;
    $query_params["results_per_page"] = (int)$results_per_page;
}

?>
<div class="container-fluid">
    <?php include "topnav.php" ?>
    <div class="row">
        <div class="col-sm-2">
            <div>
                <form method="POST" id="filter_form" action="index.php">
                    <label for="search_str">Search cars:</label>
                    <input type="text" name="search_str" class="form-control" id="search_str" value="<?php echo $search_str ?>">
                    <label for="type">Condition:</label>
                    <select name="type" class="form-control" id="type" onchange='$("#filter_form").submit();'>
                        <?php foreach($conditions as $condition) {
                            $selected = ($type === $condition) ? "selected" : "";
                            echo "<option $selected>$condition</option>";
                        } ?>
                    </select>
                    <label for="search_year">Year:</label>
                    <select name="search_year" class="form-control" id="search_year" onchange='$("#filter_form").submit();'>
                        <?php
                            $selected = (isset($search_year) and $search_year === "All") ? "selected" : "";
                            echo "<option $selected>All</option>";
                            $year_opt = 2020; while($year_opt > 1980) {
                            $selected = (isset($search_year) and $search_year === "$year_opt") ? "selected" : "";
                            echo "<option $selected>$year_opt</option>";
                            $year_opt--;
                        } ?>
                    </select>
                    <label for="search_make">Make:</label>
                    <select name="search_make" class="form-control" id="search_make" onchange='$("#filter_form").submit();'>
                        <?php
                        $selected = (isset($search_make) and $search_make === "All") ? "selected" : "";
                        echo "<option $selected>All</option>";
                        $make_opts = $pdo->query('SELECT DISTINCT make FROM vehicle ORDER BY make')->fetchAll(PDO::FETCH_COLUMN);
                        foreach($make_opts as $make_opt) {
                            $selected = (isset($search_make) and $search_make === "$make_opt") ? "selected" : "";
                            echo "<option $selected>$make_opt</option>";
                        } ?>
                    </select>
                    <label for="search_model">Model:</label>
                    <select name="search_model" class="form-control" id="search_model" onchange='$("#filter_form").submit();'>
                        <?php
                        $selected = (isset($search_model) and $search_model === "All") ? "selected" : "";
                        echo "<option $selected>All</option>";
                        if(isset($search_make) and $search_make !== "" and $search_make !== "All") {
                            $sth = $pdo->prepare("SELECT DISTINCT model FROM vehicle WHERE make = :search_make ORDER BY model");
                            $sth->bindParam("search_make", $search_make, PDO::PARAM_STR);
                            $sth->execute();
                            $model_opts = $sth->fetchAll(PDO::FETCH_COLUMN);
                            //$model_opts = $pdo->query('SELECT DISTINCT model FROM vehicle ORDER BY model')->fetchAll(PDO::FETCH_COLUMN);
                            foreach($model_opts as $model_opt) {
                                $selected = (isset($search_model) and $search_model === "$model_opt") ? "selected" : "";
                                echo "<option $selected>$model_opt</option>";
                            }
                        } ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="col-sm-10">
            <?php if($match_count > 0) { ?>
              <div class="row">
                <div class="col-sm-6">
                    <form method="POST" id="rpp_form" class="form-inline" action="index.php">
                        <label for="results_per_page">Results per page:&nbsp;</label>
                        <select name="results_per_page" class="form-control form-control-sm w-auto" id="results_per_page" onchange='$("#rpp_form").submit();'>
                            <?php foreach($result_counts as $result_count) {
                                $selected = ($results_per_page === $result_count) ? "selected" : "";
                                echo "<option $selected>$result_count</option>";
                            } ?>
                        </select>
                    </form>
                </div>
                <div class="col-sm-6">
                    <?php include "pagination.php"; ?>
                </div>
            </div>
            <div class="d-flex flex-wrap">
                <?php
                $sql = "select * from vehicle $condition_where limit :offset, :results_per_page";
                $sth = $pdo->prepare($sql);
                foreach(array_keys($query_params) as $param) {
                    $bind_type = is_int($query_params[$param]) ? PDO::PARAM_INT : PDO::PARAM_STR;
                    $sth->bindParam($param, $query_params[$param],$bind_type);
                }
                $ret = $sth->execute();
                $vehicles = $sth->fetchAll(PDO::FETCH_CLASS, 'Vehicle');
                foreach($vehicles as $vehicle) { ?>
                    <div class="card" style="width: 18rem;">
                        <div class="card-header text-center">
                            <a href="detail.php?stock=<?php echo $vehicle->stock ?>">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode( $vehicle->photo )?>" class="img-thumbnail card-img-top" alt="<?php echo $vehicle->name; ?>" width="222" height="167"/></a>
                            <br/><a href="detail.php?stock=<?php echo $vehicle->stock ?>" class="font-weight-bold text-secondary"><?php echo $vehicle->name; ?></a>
                        </div>
                        <div class="card-body text-center">
                            <div class="list-group">
                                <div class="d-flex align-items-center flex-row">
                                    <div class="d-flex flex-column">
                                        <div class="text-muted">Condition:</div>
                                    </div>
                                    <div class="d-flex flex-row ml-auto">
                                        <div class="text-muted"><?php echo $conditions[$vehicle->condition_nu]; ?></div>
                                    </div>
                                </div>
                                <?php if($vehicle->savings > 0) { ?>
                                    <div class="d-flex align-items-center flex-row">
                                        <div class="d-flex flex-column">
                                            <div class="text-muted">Retail Price:</div>
                                        </div>
                                        <div class="d-flex flex-row ml-auto">
                                            <div class="text-muted">$<?php echo number_format($vehicle->retail,2); ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if($vehicle->savings > 0) { ?>
                                    <div class="d-flex align-items-center flex-row">
                                        <div class="d-flex flex-column">
                                            <div class="text-muted">Savings Up To:</div>
                                        </div>
                                        <div class="d-flex flex-row ml-auto">
                                            <div class="text-muted">$<?php echo number_format($vehicle->savings,2); ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="d-flex align-items-center flex-row">
                                    <div class="d-flex flex-column">
                                        <div class="text-muted">Sales Price:</div>
                                    </div>
                                    <div class="d-flex flex-row ml-auto">
                                        <div class="text-muted">$<?php echo number_format($vehicle->sales,2); ?></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-row">
                                    <div class="d-flex flex-column">
                                        <div class="text-muted">Stock #:</div>
                                    </div>
                                    <div class="d-flex flex-row ml-auto">
                                        <div class="text-muted"><?php echo $vehicle->stock; ?></div>
                                    </div>
                                </div>
                                <?php if(isset($vehicle->mileage)) { ?>
                                    <div class="d-flex align-items-center flex-row">
                                        <div class="d-flex flex-column">
                                            <div class="text-muted">Mileage:</div>
                                        </div>
                                        <div class="d-flex flex-row ml-auto">
                                            <div class="text-muted"><?php echo number_format($vehicle->mileage) ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <?php include "pagination.php"; ?>
                </div>
            </div>

          <?php  } else { ?>
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <h3>No results found. Please try a different search.</h3>
                    </div>
                </div>

          <?php  }?>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
