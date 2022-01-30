<!-- Modal -->
<div class="modal fade" id="modalHelp" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Help</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <p>
To start a dialing campaign you need to create it first (Call Records -> Campaigns).<br>
Then you need to copy contacts to this campaign. Go to Contacts->Contacts, select required contacts (use SHIFT+mouse_click to select multiple contacts) and press the button "Create Campaign". Select desired campaign.
To start campaign go to Call Records->Call Records. Select a campaign. The records for this campaign should be displayed. In the upper right corner select "Start campaign". Withing 1 minute the system should start dialing numbers.
After the campaign is finished you should receive an email and records in database should update. Note, that email just a notification and contains just basic information about records. Use web interface to get all records from the campaign.<br>

It is enough to import Contacts only once (Contacts -> Load Contacts). You can just paste CSV data directly into the web form.
Another option to add contacts - to use the button "New" on the "Contacts -> Contacts" page. Also, you can edit/delete existing contacts. Select a contact by mouse click and use Edit/Delete buttons. You can also do mass edit for multiple contacts (use SHIFT+mouse_click to select more than one contact)
</p>
                <hr/>
                <p>Contacts: <a href="mailto:{{env('ADMIN_EMAIL','')}}">{{env('ADMIN_EMAIL','')}}</a></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>