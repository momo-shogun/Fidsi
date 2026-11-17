<?php include_once __DIR__ . '/header.php'; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h2 class="admin-card-title">Subscribe Form</h2>
            <p class="admin-card-subtitle">Newsletter and subscription emails</p>
        </div>
    </div>
    <div class="admin-table-wrap">
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
                $n = 1;
                $query = $mysqli->query('select * from subscribe order by id desc');
                while ($row = $query->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?= $n ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['ddate'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
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

<?php include_once __DIR__ . '/footer.php'; ?>
