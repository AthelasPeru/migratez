<!DOCTYPE">
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>migratEZ</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style>
	input {
		padding: 10px 10px;
		border-radius: 2px;
		border-color: #eee;
		margin-top: 10px;
		cursor: pointer;
	}
	.btn-download {
		border: none;
		padding: 10px 20px;
	}
	.btn-unzip {
		background: transparent;
		border: none;
		color: blue;
		text-decoration: underline;
		margin: 0;
	}
</style>
</head>
<body style="padding-top:20px">
	<section class="container">
		<nav class="navbar navbar-default">
	        <div class="container-fluid">
	          <div class="navbar-header">
	            <a class="navbar-brand" href="#link-a-githubt">MigratEZ</a>
	          </div>
	        </div><!--/.container-fluid -->
	    </nav>
		<div class="alert alert-warning" role="alert"><strong>IMPORTANTE: </strong>No olvides borrar este archivo!!!</div>
		<?php 
			if (isset($_POST['url']) && isset($_POST['file_name'])) {
				if(!copy($_POST['url'],'./'.$_POST['file_name']))
				{
				    $errors= error_get_last();
				    echo '<div class="alert alert-success" role="alert">';
				    echo "ERROR: ".$errors['type'];
				    echo "<br />\n".$errors['message'].'</div> ';
				} else {
				    echo '<div class="alert alert-success" role="alert">Archivo copiado con éxito desde '.$_POST['url'].'</div> ';
				}
			}
		?>

		<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">

	  		<h4>Ingresa la url del archivo <strong>.zip</strong></h4>
			<h4>Coloca el nombre que quieres darle a tu nuevo archivo Ejm: <strong>foo.zip</strong></h4>
			<div class="form-group">
				<label for="url">URL</label>
				<input type="text" id="url" name="url" class="form-control"	 required onchange="getFileName(this)">
			</div>
			<br />
			<div class="form-group">
				<label for="file_name">Nombre del Archivo</label>
				<input type="text" id="file_name" name="file_name" class="form-control" required/>
			</div>
			<br />
			<input type="submit" value="COPIAR" class="btn btn-success" />
		</form>
		<br />
		<div>
			<h5>
			<?php 
				function createDir($path)
				{
					if (!file_exists($path)) {
						mkdir($path, 0777, true);
					}
				}
				function unzipFile($nameFile, $unzipHome = 0)
				{
					$path = pathinfo( realpath( $nameFile ), PATHINFO_DIRNAME );
					$zip = new ZipArchive;
					$res = $zip->open($nameFile);
					if ($res === TRUE) {
						
						if ($unzipHome == 1) {
							$path = dirname($path).'/'.substr($nameFile, 0, -4);
						}
						if ($unzipHome == 2) {
							$path = $path.'/'.substr($nameFile, 0, -4);
						}
					    $zip->extractTo( $path );
					    $zip->close();
					    return '<div class="alert alert-success" role="alert">El archivo '.$nameFile.' ha sido extraido en '.$path.'</div> ' ;
					}
					else {
						return '<div class="alert alert-warning" role="alert">No se puede abrir el archivo: '.$nameFile.'</div>';
					}
				}

				if (isset($_POST['unzip_file_here'])) {
					echo unzipFile($_POST['unzip_file_here']);		
				}
				if (isset($_POST['unzip_file_home'])) {
					echo unzipFile($_POST['unzip_file_home'], 1);		
				}
				if (isset($_POST['unzip_file'])) {
					echo unzipFile($_POST['unzip_file'], 2);		
				}
				

				function delete_files($target) {
				    if(is_dir($target)){
				        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
				        
				        foreach( $files as $file )
				        {
				            delete_files( $file );      
				        }
				        rmdir( $target );
				        echo "Carpeta ".$target." eliminado exitosamente."; 	
				    } elseif(is_file($target)) {
				        unlink( $target );  
					    echo $target." eliminado exitosamente.";
				    }
				}
				
				if (isset($_POST['file_delete'])) {
					delete_files($_POST['file_delete']);
				}

				$directory = realpath(dirname(__FILE__));
				$scanned_directory = array_diff(scandir($directory), array('..', '.'));
			?>
			</h5>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Archivos</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($scanned_directory as $value): ?>
					<tr>
						<td><?= $value ?></td>
						<td>
							<?php if (substr($value,-4) == '.zip'): ?>
							<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
								<input type="hidden" name="unzip_file_here" value="<?= $value ?>" />
								<input type="submit" value="descomprimir aquí" class="btn-unzip"/>
							</form>
							<?php endif ?>
						</td>
						<td>
							<?php if (substr($value,-4) == '.zip'): ?>
							<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
								<input type="hidden" name="unzip_file" value="<?= $value ?>" />
								<input type="submit" value="descomprimir aquí con carpeta" class="btn-unzip"/>
							</form>
							<?php endif ?>
						</td>
						<td>
							<?php if (substr($value,-4) == '.zip'): ?>
							<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
								<input type="hidden" name="unzip_file_home" value="<?= $value ?>" />
								<input type="submit" value="descomprimir en la raíz" class="btn-unzip"/>
							</form>
							<?php endif ?>
						</td>
						<td>
							
							<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
								<input type="hidden" name="file_delete" value="<?= $value ?>" />
								<input type="submit" value="eliminar" class="btn-unzip"/>
							</form>
							
						</td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</section>
	<script>
		function getFileName(e) {
			var url = e.value;
			var filename = url.substring(url.lastIndexOf('/')+1);
			document.getElementById('file_name').value = filename;
		}
	</script>
</body>
</html>
