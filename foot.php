<footer>
    <hr>
    <div class="container-fluid text-center">
        <?php
        if (!empty($_SESSION['keyID'])) {
            echo '<input id="passAlong_keyID" type="hidden" value="' . $_SESSION['keyID'] . '" />';
            echo '<input id="passAlong_vCode" type="hidden" value="' . $_SESSION['vCode'] . '" />';
            //echo '<input id="passAlong_selectedCharacter" type="hidden" value="' . $_SESSION['selectedCharacter'] . '" />';
            echo '<input id="passAlong_selectedCharacter" type="hidden" value="' . $_GET['char'] . '" />';
        }
        ?>

        <p>Project EVIE, created by <a style="cursor: pointer;" onclick="getCharData('Ionaru Otsada')">Ionaru
                Otsada.</a></p>
        <br>
        <p><strong>- Disclaimer -</strong></p>
        <p>Project EVIE is still in ongoing development, many features are missing and any data displayed may be
            incorrect.</p>
        <p>Material related to EVE Online is used with limited permission of CCP Games hf. No official affiliation or
            endorsement by CCP Games hf is stated or implied.</p>
    </div>

</footer>
</div>
</div>
</div>

<div class="modal fade" id="characterModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="characterModalTitle">Character Name</h4>
            </div>
            <div class="modal-body text-center">
                <img id="characterInfoImage"
                     src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="
                     style="display: block; margin-left: auto; margin-right: auto;" class="img-responsive"
                     alt="Generic placeholder thumbnail">
                <br>
                <div id="characterinfo"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.timeago.js"></script>
<script type="text/javascript" src="js/countUp.js"></script>
<script type="text/javascript" src="js/jquery.total-storage.js"></script>
<script type="text/javascript" src="js/helperFunctions.js"></script>
<script type="text/javascript" src="js/footerJS.js"></script>
<script>
    //Enable Bootstrap modals, tabs and tooltips
    $('#myModal').on('shown.bs.modal', function () {
        $('#myInput').focus()
    });

    $('#myTabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show')
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>