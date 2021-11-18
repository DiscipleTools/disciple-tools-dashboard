<div class="card-header">
    <?php echo $card->label ?>
    <div style="display: inline-block"
        class="stats-spinner loading-spinner active"></div>
</div>
<div class="card-body card-body--scroll">
    <div class="amchart-container">
        <div class="benchmarks-chart-keys">
            <ul>
                <li>
                    <div class="benchmarks-previous-block"></div>
                    <span id="benchmarks_previous"></span>
                </li>
                <li>
                    <div class="benchmarks-current-block"></div>
                    <span id="benchmarks_current"></span>
                </li>
            </ul>
        </div>
        <div class="benchmark-chart-container">
            <div id="benchmark_chart"></div>
        </div>
    </div>
</div>