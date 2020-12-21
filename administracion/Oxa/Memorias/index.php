<?php

include '../Funciones.php';

$band =false;
if (isset($_GET['info'], $_GET['keyswords'])){
    
    if(($_GET['info'] and $_GET['keyswords'] )!=NULL){
        $band=true;
        $info= $_GET['info'];
        $keyswords= $_GET['keyswords'];
        $keyswords=strtolower ($keyswords);
       
        $ks=explode(' ', $keyswords);
        
        $str="Info: $info <hr>";
                    
        foreach ($ks as &$p ){
        $p=eliminar_simbolos($p); 
        } 
         foreach($ks as $k){
            $str.= "palabra: $k <hr>";
        }
        
        $keys=implode(',',$ks );
        
        #usuario 1: suponemos
        createInfo('1', $info,$keys );
        
        $str.= 'Informacion registrada exitosamente.';

    }

}
?>
  
   <html>
    <head>
        <title>Memorias</title>
      
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">   
    </head>
    <body>
        <div class="container p-5">
          
            <form class="" action="" method="GET">
              <div class="form-group">
                  <input type="text" class="form-control" id="info" aria-describedby="info" placeholder="Informacion" name="info">
              </div>
               <div class="form-group">
                  <input type="text" class="form-control" id="keyswords" aria-describedby="keyswords" placeholder="Keyswords" name="keyswords">
              </div>
               <div class="form-group">
                  <input type="submit" class="form-control btn-primary">
              </div>
               
            </form>
             
             <?php if ($band){?>
            <div class="alert alert-success" role="alert" >
                <?=$str?>
            </div>
              <?php }?>
        </div>
        
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
    </body>
</html>