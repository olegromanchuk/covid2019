package campaigns

import (
	"covid2019/config"
	"covid2019/utils"
	"database/sql"
)

type Campaign struct {
	Id          int32  `json:"id"`
	Name        string `json:"name"`
	Description string `json:"description"`
}

func GetCampaigns() (campaings []Campaign, err error) {

	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()

	stmt, err := Db.Prepare(`Select id, name, description FROM campaigns`)
	utils.CheckErr(err)
	//defer stmt.Close()
	rows, err := stmt.Query()
	if err != nil {
		return campaings, err
	}
	for rows.Next() {
		var campaign Campaign
		err = rows.Scan(&campaign.Id, &campaign.Name, &campaign.Description)
		if err != nil {
			return campaings, err
		}
		campaings = append(campaings, campaign)
	}
	return campaings, nil
}

//CreateCampaign creates campaign
func (campaign *Campaign) CreateCampaign() error {
	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()
	err = campaign.write(Db)
	if err != nil {
		return err
	}
	return nil
}

func DeleteCampaigns(campaigns []Campaign) (iErrors []error) {
	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()

	for _, campaign := range campaigns {
		err = campaign.delete(Db)
		if err != nil {
			iErrors = append(iErrors, err)
		}
	}
	return iErrors
}

//write writes to database
func (campaign *Campaign) write(Db *sql.DB) error {
	stmt, err := Db.Prepare(`INSERT INTO campaigns (name,description)
VALUES (?,?)`)
	utils.CheckErr(err)
	//defer stmt.Close()
	_, err = stmt.Exec(campaign.Name, campaign.Description)
	if err != nil {
		return err
	}
	return nil
}

func (campaign *Campaign) delete(Db *sql.DB) error {
	stmt, err := Db.Prepare(`DELETE FROM campaigns WHERE id=?`)
	utils.CheckErr(err)
	//defer stmt.Close()
	_, err = stmt.Exec(campaign.Id)
	if err != nil {
		return err
	}
	return nil
}
