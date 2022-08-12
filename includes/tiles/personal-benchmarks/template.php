<div class="tile-header">
    <?php echo esc_html( $tile->label ) ?>
    <div style="display: inline-block"
        class="stats-spinner loading-spinner active"></div>
</div>
<div class="tile-body tile-body--center">
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
