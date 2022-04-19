package ihandlers

import (
	"covid2019/callrecords"
	"covid2019/campaigns"
	"covid2019/contacts"
	"covid2019/utils"
	"encoding/json"
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"github.com/sirupsen/logrus"
)

type DatatablesOutWError struct {
	Error string
}

type DatatablesOut struct {
	Data interface{} `json:"data"`
}

func GetContacts(w http.ResponseWriter, req *http.Request) {

	contacts, err := contacts.GetAllContacts()

	if err != nil {
		data := map[string]string{"error": err.Error()}
		json.NewEncoder(w).Encode(data)
	} else {
		utils.PrintStructToWriter(contacts, w)
	}
}

func GetCampaigns(w http.ResponseWriter, req *http.Request) {

	contacts, err := campaigns.GetCampaigns()

	if err != nil {
		data := map[string]string{"error": err.Error()}
		json.NewEncoder(w).Encode(data)
	} else {
		utils.PrintStructToWriter(contacts, w)
	}
}

//CreateContact creates one contact
func CreateCampaign(w http.ResponseWriter, req *http.Request) {

	type DatatablesIn struct {
		Data   []campaigns.Campaign `json:"data"`
		Action string               `json:"action"`
	}

	var SliceCampaigns DatatablesIn

	err := json.NewDecoder(req.Body).Decode(&SliceCampaigns)
	utils.CheckErr(err)
	//spew.Dump(SliceCampaigns)

	campaign := SliceCampaigns.Data[0]
	err = campaign.CreateCampaign()
	if err != nil {
		result := map[string]string{"error": err.Error()}
		json.NewEncoder(w).Encode(result)
		return
	}
	//returnForDT := map[string]interface{}
	returnForDT := DatatablesOut{SliceCampaigns.Data}
	utils.PrintStructToWriter(returnForDT, w)

}

//DeleteCampaigns delete campaigns (but not call records)
func DeleteCampaigns(w http.ResponseWriter, req *http.Request) {
	type Campaign struct {
		Id          string `json:"id"`
		Name        string `json:"name"`
		Description string `json:"description"`
	}

	type DatatablesIn struct {
		Data   map[int]Campaign `json:"data"`
		Action string           `json:"action"`
	}

	var SliceCampaigns DatatablesIn

	err := json.NewDecoder(req.Body).Decode(&SliceCampaigns)
	utils.CheckErr(err)
	//spew.Dump(SliceCampaigns)

	var sliceErrors []error

	var campaignsAll []campaigns.Campaign
	for _, val := range SliceCampaigns.Data {
		//convert id from string to int
		intID, err := strconv.ParseInt(val.Id, 10, 32)
		if err != nil {
			sliceErrors = append(sliceErrors, err)
			continue
		}
		campaign := campaigns.Campaign{
			Id:          int32(intID),
			Name:        val.Name,
			Description: val.Description,
		}
		campaignsAll = append(campaignsAll, campaign)
	}

	sliceErrs := campaigns.DeleteCampaigns(campaignsAll)
	if len(sliceErrs) > 0 {
		sliceErrors = append(sliceErrors, sliceErrs...)
	}

	if len(sliceErrors) > 0 {
		var stringError = ""
		for _, val := range sliceErrors {
			stringError += val.Error() + ":"
		}
		result := map[string]string{"error": stringError}
		json.NewEncoder(w).Encode(result)
	} else {
		returnForDT := DatatablesOut{""}
		utils.PrintStructToWriter(returnForDT, w)
	}
}

func GetContact(w http.ResponseWriter, req *http.Request) {
	fmt.Println("get")

	//params := mux.Vars(req)
	//contact, err := strconv.Atoi(params["id"])
	//utils.CheckErr(err)

	//
	//contacts, err := contacts.GetContact(contact)
	//
	//if err != nil {
	//	data := map[string]string{"error": "No such customer or can not get an information"}
	//	json.NewEncoder(w).Encode(data)
	//} else {
	//	//print customer info
	//	utils.PrintStructToWriter(contacts, w)
	//}
}

func UploadContacts(w http.ResponseWriter, req *http.Request) {

	var allContacts []contacts.Contact

	var content map[string]string

	err := json.NewDecoder(req.Body).Decode(&content)
	utils.CheckErr(err)

	allRecords := strings.Split(content["numbers"], "\r\n")
	var allErrors []error

	for _, val := range allRecords {
		sliceRecord := strings.Split(val, ",")
		if len(sliceRecord) == 4 {
			contact := contacts.Contact{
				Name:        sliceRecord[0],
				PhoneNumber: sliceRecord[1],
				Email:       sliceRecord[2],
				Description: sliceRecord[3],
			}
			allContacts = append(allContacts, contact)
		} else { //error
			allErrors = append(allErrors, fmt.Errorf("Check if this record is correct:---\"%v\" --- Must have exactly 3 commas. Detected only %v ", val, len(sliceRecord)-1))
			escapedVal := strings.Replace(val, "\n", "", -1)
			escapedVal = strings.Replace(escapedVal, "\r", "", -1)
			logrus.Error(escapedVal)
		}
	}

	sliceErrors := contacts.CreateContacts(allContacts)

	allErrors = append(allErrors, sliceErrors...)

	if len(allErrors) > 0 {
		var result []map[string]string

		for _, val := range allErrors {
			err := map[string]string{"error": val.Error()}
			result = append(result, err)
		}
		json.NewEncoder(w).Encode(result)
	} else {
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(201)
	}
}

func UpdateContacts(w http.ResponseWriter, req *http.Request) {

	type Contact struct {
		Id          string `json:"id"`
		Name        string `json:"name"`
		PhoneNumber string `json:"phone_primary"`
		Email       string `json:"email"`
		Description string `json:"description"`
		Status      string `json:"status"`
	}

	type DatatablesIn struct {
		Data   map[int]Contact `json:"data"`
		Action string          `json:"action"`
	}

	var sliceErrors []error

	var allUpdatedContacts DatatablesIn

	err := json.NewDecoder(req.Body).Decode(&allUpdatedContacts)
	utils.CheckErr(err)
	//spew.Dump(allUpdatedContacts)

	var contactsAll []contacts.Contact
	for _, val := range allUpdatedContacts.Data {
		//convert id from string to int
		intID, err := strconv.ParseInt(val.Id, 10, 32)
		if err != nil {
			sliceErrors = append(sliceErrors, err)
			continue
		}
		contact := contacts.Contact{
			Id:          int32(intID),
			Name:        val.Name,
			PhoneNumber: val.PhoneNumber,
			Email:       val.Email,
			Description: val.Description,
			Status:      val.Status,
		}
		contactsAll = append(contactsAll, contact)
	}
	sliceErrs := contacts.UpdateContacts(contactsAll)
	if len(sliceErrs) > 0 {
		sliceErrors = append(sliceErrors, sliceErrs...)
	}

	if len(sliceErrors) > 0 {

		var stringError = ""
		for _, val := range sliceErrors {
			stringError += val.Error() + ":"
		}
		result := map[string]string{"error": stringError}
		json.NewEncoder(w).Encode(result)
	} else {
		//returnForDT := map[string]interface{}
		returnForDT := DatatablesOut{contactsAll}
		utils.PrintStructToWriter(returnForDT, w)
	}
}

//CreateContact creates one contact
func CreateContact(w http.ResponseWriter, req *http.Request) {

	type Contact struct {
		Id          string `json:"id,omitempty"`
		Name        string `json:"name"`
		PhoneNumber string `json:"phone_primary"`
		Email       string `json:"email,omitempty"`
		Description string `json:"description,omitempty"`
		Status      string `json:"status"`
	}

	type DatatablesIn struct {
		Data   []Contact `json:"data"`
		Action string    `json:"action"`
	}

	var sliceErrors []error

	var allCreatedContacts DatatablesIn

	err := json.NewDecoder(req.Body).Decode(&allCreatedContacts)
	utils.CheckErr(err)
	//spew.Dump(allCreatedContacts)

	var contactsAll []contacts.Contact
	for _, val := range allCreatedContacts.Data {
		contact := contacts.Contact{
			Name:        val.Name,
			PhoneNumber: val.PhoneNumber,
			Email:       val.Email,
			Description: val.Description,
			Status:      val.Status,
		}
		contactsAll = append(contactsAll, contact)
	}
	sliceErrs := contacts.CreateContacts(contactsAll)
	if len(sliceErrs) > 0 {
		sliceErrors = append(sliceErrors, sliceErrs...)
	}

	if len(sliceErrors) > 0 {

		var stringError = ""
		for _, val := range sliceErrors {
			stringError += val.Error() + ":"
		}
		result := map[string]string{"error": stringError}
		json.NewEncoder(w).Encode(result)
	} else {
		//returnForDT := map[string]interface{}
		returnForDT := DatatablesOut{contactsAll}
		utils.PrintStructToWriter(returnForDT, w)
	}
}

//CreateCampaignCallRecords creates campaign from set of contacts
func CreateCampaignCallRecords(w http.ResponseWriter, req *http.Request) {

	type CampaignRecord struct {
		Id          string `json:"id"`
		Name        string `json:"name"`
		PhoneNumber string `json:"phone_primary"`
		Email       string `json:"email,omitempty"`
		Description string `json:"description,omitempty"`
		Status      string `json:"status"`
		CampaignID  string `json:"campaign_id"`
	}

	type StructReturn struct {
		ErrorRecords   []string `json:"error,omitempty"`
		CreatedRecords int      `json:"created_records"`
	}

	type DatatablesIn struct {
		Data   map[int]CampaignRecord `json:"data"`
		Action string                 `json:"action"`
	}

	var sliceErrors []error
	var allContacts DatatablesIn

	err := json.NewDecoder(req.Body).Decode(&allContacts)
	utils.CheckErr(err)
	//spew.Dump(allContacts)

	var campaignRecordsAll []callrecords.CampaignRecord
	for _, val := range allContacts.Data {
		campaignRecord := callrecords.CampaignRecord{
			Name:        val.Name,
			PhoneNumber: val.PhoneNumber,
			Email:       val.Email,
			Processed:   "0",
			CampaignID:  val.CampaignID,
			Description: val.Description,
		}
		campaignRecordsAll = append(campaignRecordsAll, campaignRecord)
	}
	amountOfCreatedRecords, sliceErrs := callrecords.CreateCampaign(campaignRecordsAll)
	if len(sliceErrs) > 0 {
		sliceErrors = append(sliceErrors, sliceErrs...)
	}

	if len(sliceErrors) > 0 {
		var stringError []string
		for _, val := range sliceErrors {
			stringError = append(stringError, val.Error())
		}
		result := StructReturn{CreatedRecords: amountOfCreatedRecords, ErrorRecords: stringError}
		//spew.Dump(result)
		json.NewEncoder(w).Encode(result)
	} else {
		//returnForDT := map[string]interface{}
		returnForDT := StructReturn{CreatedRecords: amountOfCreatedRecords}
		utils.PrintStructToWriter(returnForDT, w)
	}
}

func DeleteContacts(w http.ResponseWriter, req *http.Request) {

	type Contact struct {
		Id          string `json:"id"`
		Name        string `json:"name"`
		PhoneNumber string `json:"phone_primary"`
		Email       string `json:"email"`
		Description string `json:"description"`
		Status      string `json:"status"`
	}

	type DatatablesIn struct {
		Data   map[int]Contact `json:"data"`
		Action string          `json:"action"`
	}

	var sliceErrors []error
	var allDeletedContacts DatatablesIn

	err := json.NewDecoder(req.Body).Decode(&allDeletedContacts)
	utils.CheckErr(err)
	//spew.Dump(allUpdatedContacts)

	var contactsAll []contacts.Contact
	for _, val := range allDeletedContacts.Data {
		//convert id from string to int
		intID, err := strconv.ParseInt(val.Id, 10, 32)
		if err != nil {
			sliceErrors = append(sliceErrors, err)
			continue
		}
		contact := contacts.Contact{
			Id:          int32(intID),
			Name:        val.Name,
			PhoneNumber: val.PhoneNumber,
			Email:       val.Email,
			Description: val.Description,
			Status:      val.Status,
		}
		contactsAll = append(contactsAll, contact)
	}
	sliceErrs := contacts.DeleteContacts(contactsAll)
	if len(sliceErrs) > 0 {
		sliceErrors = append(sliceErrors, sliceErrs...)
	}

	if len(sliceErrors) > 0 {
		var stringError = ""
		for _, val := range sliceErrors {
			stringError += val.Error() + ":"
		}
		result := map[string]string{"error": stringError}
		json.NewEncoder(w).Encode(result)
	} else {
		//returnForDT := map[string]interface{}
		returnForDT := DatatablesOut{""}
		utils.PrintStructToWriter(returnForDT, w)
	}
}
