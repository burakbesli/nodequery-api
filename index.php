<?php
ob_start();
session_start();
require("sistem.php");
$api = new ApiSystem;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="language" content="Turkish"/>
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Sunucu Durumları</title>
	<meta name="description" content="Faydata sunucu durumları gözlemleme aracıdır.">
	<link rel="shortcut icon" href="dosyalar/fav.png" title="Favicon"/>
	<link rel="icon" href="dosyalar/fav.png" title="Favicon"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>


</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="/">Sunucu Durumları</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
     </nav>
     <main role="main">
      <div class="jumbotron">
        <div class="container">
          <h1 class="display-3">Sunucu Durumları</h1>
          <p>Sunucu durumlarını aşağıdan gözlemleyebilir mevcut kullanım oranları hakkında detaylı gözlemleme yapabilirsiniz.</p>
        </div>
      </div>
      <div class="container">


<?php

	$get = $_GET['case'] ?? "";

	switch ($get) {

		default:
			echo '<style>ul{margin-left: -38px;}li{padding: 2px;border: 1pt solid #343a40;border-radius: 10px;margin-bottom: 3px;list-style-type: none;}.bosluk{padding : 5px;}.ortala{text-align: center;}</style>';
			$list = array('id','status','availability','update_time','name','load_percent','load_average','ram_total','ram_usage','disk_total','disk_usage','ipv4');
			echo '<div class="row">';
			$server_output = $api->islem();
			$data = $server_output->data[0];
			foreach ($data as $key => $value)
			{
				echo '<div class="col-md-3"><h4>'.$value->name.'</h4><p><ul>';
				foreach ($value as $a => $b)
				{
					$b = ($a == "ram_total") ? $api->formatSizeUnits($b) : $b ;
					$b = ($a == "ram_usage") ? $api->formatSizeUnits($b) : $b ;
					$b = ($a == "disk_total") ? $api->formatSizeUnits($b) : $b ;
					$b = ($a == "disk_usage") ? $api->formatSizeUnits($b) : $b ;
					if(in_array($a , $list))
					{
						echo '<li> <span class="bosluk"> '.ucwords($a).' : '.$b.' </span> </li>';
					}
				}
				echo '</ul></p>';
				echo '<p class="ortala"><a class="btn btn-primary" href="/?case=detay&id='.$value->id.'" role="button">Detayları Gör &raquo;</a></p>';
	          	echo '</div><hr>';
			}

			echo '</div>';
		break;


		case 'detay':

			$gRam = "";
			$gCom = "";

			echo '<style>.table td, .table th {padding: .1rem !important;vertical-align: top !important;border-top: 1px solid #e9ecef !important;}</style>';
			$id = $_GET['id'] ?? "";
			if($id == "" || !is_numeric($id))
			{
				echo "Sadece ID değerei girebilirsiniz!";
				break;
			}
			$server_output = $api->detay($id)->islem();
			$data = $server_output->data['0'];
			$islemler = $server_output->data['0']->processes_array;



			echo '<ul class="list-group">';
  			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Sunucu ID<span class="badge badge-primary badge-pill">'.$data->id.'</span></li>';
  			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Durumu<span class="badge badge-primary badge-pill">'.$data->status.'</span></li>';
  			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Uptime Oranı<span class="badge badge-primary badge-pill">'.$data->availability.'</span></li>';
  			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Adı<span class="badge badge-primary badge-pill">'.$data->name.'</span></li>';
  			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Aktif Oturum<span class="badge badge-success badge-pill">'.$data->sessions.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Aktif İşlem <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#aktifIslemler">işlemleri gör</button><span class="badge badge-danger badge-pill">'.$data->processes.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">İşletim Sistemi Kernel<span class="badge badge-warning badge-pill">'.$data->os_kernel.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">İşletim Sistemi Adı<span class="badge badge-warning badge-pill">'.$data->os_name.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">İşletim Sistemi Bit<span class="badge badge-warning badge-pill">'.$data->os_arch.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">CPU Adı<span class="badge badge-warning badge-pill">'.$data->cpu_name.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">CPU Frekansı<span class="badge badge-warning badge-pill">'.$data->cpu_freq.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Yük %<span class="badge badge-warning badge-pill">%'.$data->load_percent.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Yük Ortalaması<span class="badge badge-warning badge-pill">'.$data->load_average.'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Toplam RAM<span class="badge badge-info badge-pill">'.$api->formatSizeUnits($data->ram_total).'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Kullanılan RAM<span class="badge badge-info badge-pill">'.$api->formatSizeUnits($data->ram_usage).'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Toplam SWAP<span class="badge badge-info badge-pill">'.$api->formatSizeUnits($data->swap_total).'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Kullanılan SWAP<span class="badge badge-info badge-pill">'.$api->formatSizeUnits($data->swap_usage).'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Toplam Disk<span class="badge badge-info badge-pill">'.$api->formatSizeUnits($data->disk_total).'</span></li>';
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">Kullanılan Disk<span class="badge badge-info badge-pill">'.$api->formatSizeUnits($data->disk_usage).'</span></li>';
  			echo '<li class="list-group-item d-flex justify-content-between align-items-center">IP Adresi<span class="badge badge-light badge-pill">'.$data->nic.': '.$data->ipv4.'</span></li>';
			echo '</ul>';
			echo '<div class="modal fade" id="aktifIslemler" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">';
		 	echo '<div class="modal-dialog modal-lg" role="document">';
			echo '<div class="modal-content">';
			echo '<div class="modal-header">';
			echo '<h5 class="modal-title" id="exampleModalLongTitle">Sunucu '.$data->name.' için aktif işlemler</h5>';
			echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
			echo '<span aria-hidden="true">&times;</span>';
	      	echo '</button>';
	     	echo '</div>';
	     	echo '<div class="modal-body">';
	     	echo '<canvas id="myChart"></canvas>';

			echo '<table class="table table-striped table-bordered" cellspacing="0" width="100%">';
			echo '<thead>';
			echo '<tr>';
		  	echo '<th>Komut</th>';
    	    echo '<th>Sayı</th>';
 	        echo '<th>CPU</th>';
            echo '<th>Ram</th>';
	        echo '<th>Kullanıcı</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
		      		foreach ($islemler as $a => $b)
					{
						echo '<tr>';
						echo '<td>'.$b->command.'</td>';
						echo '<td>'.$b->count.'</td>';
						echo '<td>'.$b->cpu.'</td>';
						echo '<td>'.$api->formatSizeUnits($b->memory).'</td>';
						echo '<td>'.$b->user.'</td>';
						echo '</tr>';

						$gRam .= '"'.$b->command.'",';
						$gCom .= $b->count.',';
		      		}
		    echo'</tbody>';
			echo'</table>';
			echo'</div>';
			echo'</div>';
			echo'</div>';
			echo'</div>';

			?>

			<script>
			var ctx = document.getElementById('myChart').getContext('2d');
			var chart = new Chart(ctx, {

			    type: 'line',

			    data: {
			        labels: [<?=$gRam;?>],
			        datasets: [{
			            label: "İşlem Sayısı",
			            backgroundColor: '#007bff',
			            borderColor: '#007bff',
			            data: [<?=$gCom;?>],
			        }]
			    },

			    options: {}
			});
			</script>

			<?php break; }
  ?>
        <hr>
      </div>
    </main>
    <footer class="container"><p>&copy; Burak Beşli</p></footer>
</body>
</html>