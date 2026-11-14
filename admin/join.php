<?php include_once __DIR__ . '/header.php'; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h2 class="admin-card-title">Join Form</h2>
            <p class="admin-card-subtitle">Membership and company interest submissions</p>
        </div>
    </div>
    <div class="admin-table-wrap">
        <div class="table-responsive">
            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Company Website</th>
                        <th>Company Nature</th>
                        <th>Full Name</th>
                        <th>Phone Number</th>
                        <th>Email Address</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $n = 1;
                $query = $mysqli->query('select * from fds_join order by id desc');
                while ($row = $query->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?= $n ?></td>
                        <td><?= htmlspecialchars($row['co_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['co_website'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['co_nature'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
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
