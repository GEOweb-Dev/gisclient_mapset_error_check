<div id="mapfiles_error_check" style="display:none;" data-title="Controllo Mappe">
    <form id="frm_mapset_error_check", name="frm_mapset_error_check">
        <table border="1" cellpadding="3" class="stiletabella">
    	<tr class="ui-widget ui-state-default">
    		<th><?php echo GCauthor::t('mapset_list') ?></th>
    		<th><?php echo GCAuthor::t('temporary') ?></th>
    		<th><?php echo GCAuthor::t('public') ?></th>
    	</tr>
    	<?php
    	if(isset($mapsets)) {
    		foreach($mapsets as $mapset) {
    			echo '<tr>
    				<td>'.$mapset['mapset_title'].' ('.$mapset['mapset_name'].')</td>
    				<td style="text-align:center;"><input type="checkbox" value="tmp.'.$mapset['project_name'].'/'.$mapset['mapset_name'].'"></td>
                    <td style="text-align:center;"><input type="checkbox" value="'.$mapset['project_name'].'/'.$mapset['mapset_name'].'"></td>
    			</tr>';
    		}
    	}
    	?>
    	</table>
        <div style="clear: both;"></div>
        <div class="copyDivLeft">Rileva errori per:</div>
        <div class="copyDivRight">
            <input type="radio" name="check_object_type" value="layergroups">Layergroups
            <input type="radio" name="check_object_type" value="layers"> Layers
        </div>
        <div style="clear: both;"></div>
        <div class="copyDivLeft">Spedisci all'indirizzo:</div>
        <div class="copyDivRight">
            <input class="textInput" type="text" id="checkMapsetMail">
        </div>
        <div style="clear: both;"></div>
        <hr>
        <a class="button" data-action="mapfiles_error_check_execute">Esegui Controllo Mappe</a>
    </form>
</div>

<script language='Javascript'>
    $('#mapfiles_manager').prepend('<a class="button" data-action="mapfiles_error_check">Controllo Mappe</a><div><hr></div>');
    $(document).ready(function() {
        $('div#mapfiles_error_check').dialog({
            autoOpen: false,
            title: $('div#mapfiles_error_check').attr('data-title'),
            modal: true,
            width: 800,
            height: 600
        });
        $('a[data-action="mapfiles_error_check"]').click(function(event) {
            event.preventDefault();
            $('div#mapfiles_error_check').dialog('open');
        });
        $('a[data-action="mapfiles_error_check_execute"]').click(function(event) {
            event.preventDefault();
            var mapfileList = '';
            var mailAddress = '';
            var checkSingleLayers = '';
            // **** Check if sendmail is installed
            var sendmail='<?php $res=shell_exec('which sendmail'); echo trim($res); ?>';
            if (!sendmail) {
                $('#error_dialog').html('sendmail non installato, impossibile procedere');
                $('#error_dialog').dialog({
                    width: 600,
                    title: 'Error'
                });
                //return;
            }
            $('#mapfiles_error_check').find("input[type=checkbox]").each(function () {
                if (this.checked) {
                    mapfileList += $(this).val() + ' ';
                }
            });
            mapfilelist = mapfileList.trim();
            if(!mapfileList) {
                $('#error_dialog').html('nessuna mappa selezionata, impossibile procedere');
                $('#error_dialog').dialog({
                    width: 600,
                    title: 'Error'
                });
                return;
            }

            checkSingleLayers = $('input[name=check_object_type]:checked', '#frm_mapset_error_check').val();
            alert(checkSingleLayers);
            mailAddress = $('#checkMapsetMail').val();

            var params = {
                mapfiles: mapfileList,
                mail_address: mailAddress,
                check_single_layers: (checkSingleLayers=='layers'?1:0)
            }

            $.ajax({
                url: 'ajax/mapset_error_check.php',
                type: 'POST',
                dataType: 'json',
                data: params,
                success: function(response) {
                    if(typeof(response) != 'object' || typeof(response.result) == 'undefined') {
                        $('#error_dialog').html('Errore inatteso durante il lancio del controllo mappe o mancata risposta dal servizio.<br>Contattare un amministratore di sistema.');
                        $('#error_dialog').dialog({
                            width: 600,
                            title: 'Error'
                        });
                    }
                    if(response.result != 'ok') {
                        if(response.result == 'error' && typeof(response.error) != 'undefined') {
                            var errText = response.error;
                            $('#error_dialog').html(errText);
                            $('#error_dialog').dialog({
                                width: 600,
                                title: 'Error'
                            });
                            return;
                        }
                        var errText = "Errore non riconosciuto durante il lancio del controllo mappe.<br>Dettagli dell'errore: ";
                        errText += response.result + '<br>Contattare un amministratore di sistema.';
                        $('#error_dialog').html(errText);
                        $('#error_dialog').dialog({
                            width: 600,
                            title: 'Error'
                        });
                        return;
                    }
                    $('#error_dialog').html('<b style="color:black;">Il controllo per le mappe selezionate è stato avviato.<br>Al termine verrà inviato un messaggio di posta elettronica contenente il report errori.</b>');
                    $('#error_dialog').dialog({
                        width: 600,
                        title: 'Esecuzione Controllo Mappe'
                    });
                },
                error: function(obj, textStatus, errorThrown ) {
                    var errText = "Errore durante il lancio del controllo mappe, mancata risposta dal servizio.<br>Dettagli dell'errore: ";
                    errText += errorThrown + '<br>Contattare un amministratore di sistema.';
                    $('#error_dialog').html(errText);
                    $('#error_dialog').dialog({
                        width: 600,
                        title: 'Error'
                    });
                }
            });

        });
    });
</script>
