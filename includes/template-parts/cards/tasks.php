<div class="item"
     style="flex-basis: 50%">
    <div class="card">
        <span class="card-title"
              style="text-align: center; margin-bottom: 15px">
            <?php esc_html_e("Tasks", 'disciple-tools-dashboard') ?>
            <div id="tasks-spinner"
                 style="display: inline-block"
                 class="stats-spinner loading-spinner active">
            </div>
        </span>
        <ul class="existing-tasks"></ul>
        <?php
        include __dir__ . '/remove.php'
        ?>
    </div>

</div>
