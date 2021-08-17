<div class="card-header">
    <?php echo $card->label ?>
    <div style="display: inline-block"
        class="stats-spinner loading-spinner active"></div>
</div>
<div class="card-body">
    <div style="flex-basis: 25%">
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
    <div style="flex-basis: 75%">
        <div id="benchmark_chart"
            style="height: 300px"></div>
    </div>
</div>