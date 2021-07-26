<div class="item"
     style="flex-basis: 100%">
    <div class="card">
        <div style="display: flex; flex-wrap: wrap">
            <div style="flex-basis: 40%">
                <h2 style="margin:50px; display: inline-block"><?php esc_html_e("Personal Benchmarks", 'disciple-tools-dashboard') ?>
                    <div style="display: inline-block"
                         class="stats-spinner loading-spinner active"></div>
                </h2>

                <ul style="list-style: none; margin-left: 50px">
                    <li>
                        <div style="background-color: #C7E3FF; border-radius: 5px; height: 20px; width:20px; display: inline-block"></div>
                        <span id="benchmarks_previous"
                              style="vertical-align: text-bottom"></span>
                    </li>
                    <li>
                        <div style="background-color: #3f729b; border-radius: 5px; height: 20px; width:20px; display: inline-block"></div>
                        <span id="benchmarks_current"
                              style="vertical-align: text-bottom"></span>
                    </li>
                </ul>

            </div>
            <div style="flex-basis: 60%">
                <div id="benchmark_chart"
                     style="height: 300px"></div>
            </div>
        </div>
        <?php include __dir__ . '/remove.php' ?>

    </div>

</div>
