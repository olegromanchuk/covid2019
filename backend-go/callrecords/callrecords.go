package callrecords

import (
	"database/sql"
	"errors"
	"fmt"
	"github.com/go-sql-driver/mysql"
	"github.com/xyrk/covid2019/config"
	"github.com/xyrk/covid2019/utils"
	"time"
)

type CampaignRecord struct {
	Id          int32
	Name        string    `json:"name"`
	PhoneNumber string    `json:"phone_number"`
	Email       string    `json:"phone_number"`
	Processed   string    `json:"processed"`
	DateTime    time.Time `json:"processed_datatime"`
	Description string    `json:"phone_number"`
	CampaignID  string    `json:"campaign_id"`
}

func CreateCampaign(records []CampaignRecord) (amountOfCreatedRecords int, iErrors []error) {
	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()

	recordCounter := 0
	for _, campaignRecord := range records {
		err = campaignRecord.write(Db)
		if err != nil {
			iErrors = append(iErrors, err)
		} else {
			recordCounter++
		}
	}
	return recordCounter, iErrors
}

func (campaignRecord *CampaignRecord) write(Db *sql.DB) error {
	stmt, err := Db.Prepare(`INSERT INTO callrecords (main_contact,main_contact_phone,email_address,description, campaign_id)
VALUES (?,?,?,?,?)`)
	utils.CheckErr(err)
	_, err = stmt.Exec(campaignRecord.Name, campaignRecord.PhoneNumber, campaignRecord.Email, campaignRecord.Description, campaignRecord.CampaignID)
	if err != nil {
		me, ok := err.(*mysql.MySQLError)
		if !ok {
			return err
		}
		if me.Number == 1062 {
			return errors.New(fmt.Sprintf("The number %v already exists in the campaign",campaignRecord.PhoneNumber ))
		}
		return err
	}
	return nil
}
