<?php include_once'header.php';?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css">

<div class="mx-4">
    <div class="row">
        <?php include_once'leftlink.php';?>
        <div class="col-lg-9">
            <div class="bg-white p-4">
                <div class="d-flex justify-content-between align-item-center">
                    <h5>Appointment</h5>
                    <!--<a href="" class="btn btn-outline-primary btn-sm">Add Category</a>  -->
                </div>
                <hr>
                <div class="table-responsive">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $n=1;
                    $query = $mysqli->query("select * from subscribe order by id desc");
                    while($row = $query->fetch_assoc()){
                    ?>
                    <tr>
                        <td><?=$n?></td>
                        <td><?=$row['email']?></td>
                        <td><?=$row['ddate']?></td>
                    </tr>
                    <?php
                    $n++;
                    }
                    ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#example').DataTable();
});
</script>
</body>
</html>