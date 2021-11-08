<div class="dash-card item"
     data-card-handle="<?php echo $card->handle; ?>"
     id="dash-card--<?php echo $card->handle; ?>"
     style="--dashboard-card-column: span <?php echo $card->span; ?>;"
>

     <div class="card">
          <?php $card->render(); ?>
          <div class="card-footer">
               <span class="card-nav-btn" onclick="toggleCardNav('<?php echo $card->handle; ?>')">
               <svg height="20" viewBox="0 0 4 20" width="20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="2"/><circle cx="2" cy="10" r="2"/><circle cx="2" cy="18" r="2"/></svg>
               </span>
               
               <div class="card-nav" id="card-nav-<?php echo $card->handle; ?>">
                    <div class="card-move-left" onclick="dt_dashboard.moveBack('<?php echo $card->handle; ?>')">
                         <i class="fi-arrow-left"></i> Move Left
                    </div>
                    <div class="card-move-right" onclick="dt_dashboard.moveForward('<?php echo $card->handle; ?>')">
                         <i class="fi-arrow-right"></i> Move Right
                   </div>
                   <div class="card-remove" onclick="dt_dashboard.remove('<?php echo $card->handle; ?>')">
                        <i class="fi-x"></i> Remove Tile
                    </div>
               </div>


               
               

          </div>
     </div>
</div>

<script>
     function toggleCardNav($id) {
          var element = document.getElementById("card-nav-" + $id);
          element.classList.toggle("show");
          
          var blank = document.getElementById("blank");
          blank.classList.toggle("show");
          console.log("blankkk");


          blank.onclick = function(){
               element.classList.toggle("show");
               blank.classList.toggle("show");
          };

     }
</script>