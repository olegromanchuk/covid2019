package contacts

import (
	"bytes"
	"database/sql"
	"errors"
	"fmt"
	"github.com/go-sql-driver/mysql"
	_ "github.com/go-sql-driver/mysql"
	"github.com/improcom/covid2019-predictive-dialer-backend-go/config"
	"github.com/improcom/covid2019-predictive-dialer-backend-go/utils"
	"regexp"
)

type Contact struct {
	Id          int32  `json:"id"`
	Name        string `json:"name"`
	PhoneNumber string `json:"phone_primary"`
	Email       string `json:"email"`
	Description string `json:"description"`
	Status      string `json:"status"`
}

type ContactSQL struct {
	Id          sql.NullInt32  `json:"id"`
	Name        sql.NullString `json:"name"`
	PhoneNumber sql.NullString `json:"phone_primary"`
	Email       sql.NullString `json:"email"`
	Description sql.NullString `json:"description"`
	Status      sql.NullString `json:"status"`
}

func GetAllContacts() (contacts []Contact, err error) {

	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()

	stmt, err := Db.Prepare(`Select id, name,phone_primary,email,description, status FROM contacts`)
	utils.CheckErr(err)
	//defer stmt.Close()
	rows, err := stmt.Query()
	if err != nil {
		return contacts, err
	}
	for rows.Next() {
		var contactSQL ContactSQL
		var contact Contact
		rows.Scan(&contactSQL.Id, &contactSQL.Name, &contactSQL.PhoneNumber, &contactSQL.Email, &contactSQL.Description, &contactSQL.Status)
		contact.Id = contactSQL.Id.Int32
		contact.Name = contactSQL.Name.String
		contact.Email = contactSQL.Email.String
		contact.PhoneNumber = contactSQL.PhoneNumber.String
		contact.Description = contactSQL.Description.String
		contact.Status = contactSQL.Status.String
		contacts = append(contacts, contact)
	}
	return contacts, nil
}

func GetContact(id int) (contacts []Contact, err error) {

	democontact := Contact{
		Id:          1,
		Name:        "sadasda",
		PhoneNumber: "123123123",
		Email:       "asda@masda.net",
		Description: "asdasda",
	}
	//var contact Contact
	contacts = append(contacts, democontact)
	return contacts, nil
}

func UpdateContacts(contacts []Contact) (iErrors []error) {
	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()

	reg, err := regexp.Compile("[^0-9]+")
	utils.CheckErr(err)

	for _, contact := range contacts {
		//prepare phone number
		phoneRaw := contact.PhoneNumber
		phoneAccurate := reg.ReplaceAllString(phoneRaw, "")
		if len(phoneAccurate) == 10 {
			var iBuffer bytes.Buffer
			for i, rune := range phoneAccurate {
				switch i {
				case 0:
					iBuffer.WriteRune(rune)
				case 1:
					iBuffer.WriteRune(rune)
				case 2:
					iBuffer.WriteRune(rune)
				case 3:
					iBuffer.WriteString("-")
					iBuffer.WriteRune(rune)
				case 4:
					iBuffer.WriteRune(rune)
				case 5:
					iBuffer.WriteRune(rune)
				case 6:
					iBuffer.WriteString("-")
					iBuffer.WriteRune(rune)
				case 7:
					iBuffer.WriteRune(rune)
				case 8:
					iBuffer.WriteRune(rune)
				case 9:
					iBuffer.WriteRune(rune)
				}
				contact.PhoneNumber = iBuffer.String()
			}
		} else {
			errNotValidPhoneNumber := errors.New(fmt.Sprintf("The number: %v is not valid US number. Must be 10 digits long.", phoneAccurate))
			iErrors = append(iErrors, errNotValidPhoneNumber)
			continue
		}
		//set status enabled by default
		//contact.Status="active"
		err = contact.update(Db)
		if err != nil {
			iErrors = append(iErrors, err)
		}
	}
	return iErrors
}

func DeleteContacts(contacts []Contact) (iErrors []error) {
	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()

	for _, contact := range contacts {
		err = contact.delete(Db)
		if err != nil {
			iErrors = append(iErrors, err)
		}
	}
	return iErrors
}

//CreateContacts creates multiple contacts
func CreateContacts(contacts []Contact) (iErrors []error) {
	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()

	reg, err := regexp.Compile("[^0-9]+")
	utils.CheckErr(err)

	for _, contact := range contacts {
		//prepare phone number
		phoneRaw := contact.PhoneNumber
		phoneAccurate := reg.ReplaceAllString(phoneRaw, "")
		if len(phoneAccurate) == 10 {
			var iBuffer bytes.Buffer
			for i, rune := range phoneAccurate {
				switch i {
				case 0:
					iBuffer.WriteRune(rune)
				case 1:
					iBuffer.WriteRune(rune)
				case 2:
					iBuffer.WriteRune(rune)
				case 3:
					iBuffer.WriteString("-")
					iBuffer.WriteRune(rune)
				case 4:
					iBuffer.WriteRune(rune)
				case 5:
					iBuffer.WriteRune(rune)
				case 6:
					iBuffer.WriteString("-")
					iBuffer.WriteRune(rune)
				case 7:
					iBuffer.WriteRune(rune)
				case 8:
					iBuffer.WriteRune(rune)
				case 9:
					iBuffer.WriteRune(rune)
				}
				contact.PhoneNumber = iBuffer.String()
			}
		} else {
			reassembledContact := fmt.Sprintf("%v,%v,%v,%v", contact.Name, contact.PhoneNumber,contact.Email,contact.Description)
			errNotValidPhoneNumber := errors.New(fmt.Sprintf("The number: %v in the line -- %v -- is not valid US number. Must be 10 digits long.", phoneAccurate,reassembledContact))
			iErrors = append(iErrors, errNotValidPhoneNumber)
			continue
		}
		//set status enabled by default
		contact.Status = "Active"
		err = contact.write(Db)
		if err != nil {
			iErrors = append(iErrors, err)
		}
	}
	return iErrors
}

//SetContact updates contact. Id must be persistent
func (contact *Contact) SetContact(Db *sql.DB) error {
	Db, err := sql.Open("mysql", config.SQLDB)
	utils.CheckErr(err)
	defer Db.Close()
	err = contact.write(Db)
	if err != nil {
		return err
	}
	return nil
}

func (contact *Contact) write(Db *sql.DB) error {
	stmt, err := Db.Prepare(`INSERT INTO contacts (name,phone_primary,email,description, status)
VALUES (?,?,?,?,?)`)
	utils.CheckErr(err)
	//defer stmt.Close()
	_, err = stmt.Exec(contact.Name, contact.PhoneNumber, contact.Email, contact.Description, contact.Status)
	if err != nil {
		me, ok := err.(*mysql.MySQLError)
		if !ok {
			return err
		}
		if me.Number == 1062 {
			return errors.New(fmt.Sprintf("The number %v already exists in the system",contact.PhoneNumber ))
		}
		return err
	}
	return nil
}

func (contact *Contact) delete(Db *sql.DB) error {
	stmt, err := Db.Prepare(`DELETE FROM contacts WHERE id=?`)
	utils.CheckErr(err)
	//defer stmt.Close()
	_, err = stmt.Exec(contact.Id)
	if err != nil {
		return err
	}
	return nil
}

func (contact *Contact) update(Db *sql.DB) error {
	stmt, err := Db.Prepare(`UPDATE contacts SET name=?,phone_primary=?,email=?,description=?, status=? WHERE id=?`)
	utils.CheckErr(err)
	//defer stmt.Close()
	_, err = stmt.Exec(contact.Name, contact.PhoneNumber, contact.Email, contact.Description, contact.Status, contact.Id)
	if err != nil {
		return err
	}
	return nil
}

func (contact *Contact) get(Db *sql.DB) error {
	stmt, err := Db.Prepare(`Select id, name,phone_primary,email,description, status FROM contacts where id=?`)
	utils.CheckErr(err)
	//defer stmt.Close()
	err = stmt.QueryRow(contact.Id).Scan(contact)
	if err != nil {
		return err
	}
	return nil
}
