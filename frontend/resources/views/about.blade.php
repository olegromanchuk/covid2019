<!-- Modal -->
<div class="modal fade" id="modalAbout" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Automated Dialing System (open source) - ADSoS</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <p>Version 2.1</p>
                Added support for AWS Chime
                <hr/>

                <p>Version 2.0</p>
                Redesigned backend architecture: <br>
                - runs on Ubuntu<br>
                - unified repo for backend and frontend
                <hr/>

                <p>Version 1.6</p>
                Changelog
                <hr/>

                <p>Version 1.5</p>
                Three simultaneous calls are now allowed.
                <hr/>

                <p>Version 1.4</p>
                Added status for running campaign.
                <hr/>
                <p>Version 1.3</p>
                Added restriction and validation for repetitive phone numbers. Only unique phone numbers are allowed in contacts and campaign records.
                <hr/>
                <p>Contacts: <a href="mailto:{{env('ADMIN_EMAIL','')}}">{{env('ADMIN_EMAIL','')}}</a></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
