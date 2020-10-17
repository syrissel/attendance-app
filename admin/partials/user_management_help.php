<?php
// Get get absolute path of current file.
require('../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($root_path);
$dotenv->load();
$relative_root = $_ENV['ROOT'];
?>

<div class="modal fade" id="modal_user_help" tabindex="-1" role="dialog" aria-labelledby="modal_user_help_title" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_user_help_title"><i class="fa fa-info-circle" aria-hidden="true"></i> User Management - Help</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="d-flex flex-column border border-secondary rounded float-right px-3 py-2 ml-1 text-left">
            <h6><u>Table of Contents</u></h6>
            <button class="section_links btn btn-link p-0 text-left" data-section="section_1">1. Navigation</button>
          </div>
          <h4 id="section_1"><u>Navigation</u></h4>
          <p>Use the navigation menu to easily return to a previous form.</p>
          <img src="<?= $relative_root ?>images/navigation.png" style="width:400px;" alt="navigation.png">
          <p>The attendances link will bring you back to the attendance search form, and the user profile link will bring you back to that user's profile.</p>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
