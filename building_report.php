<?php include('nav.php') ?>
<div class="container">
    <h4 class="mt-4 text-center" >Who's here? - <?= date('M j, Y H:i:s') ?></h4><a class="text-decoration-none float-right" id="print_link" href="#" onclick="window.print();return false;">Print</a>
    <div class="table-responsive">
<table class="table table-striped table-bordered mt-2" style="table-layout: fixed;">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (User::getAllUsers() as $user): ?>
            <tr>
            <td><?= $user->getFullName() ?></td>
            <td><?= $user->getPrettyBuildingStatus() ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
</div>
</div>