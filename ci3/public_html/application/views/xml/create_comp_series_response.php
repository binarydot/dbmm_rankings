<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php if ($response['status'] == 'OK'): ?>
<response status="0">
	<id><?=$response['ID']?></id>
	<name><?=$response['name']?></name>
	<message></message>
</response>
<?php else: ?>
<response status="1">
	<message>
		An error occurred creating the new competition series. <?=$response['errorMessage']?>
	</message>
</response>
<?php endif; ?>