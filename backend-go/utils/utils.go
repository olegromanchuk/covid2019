package utils

import (
	"encoding/json"
	"github.com/sirupsen/logrus"
	"net/http"
	"runtime"
)

func PrintStructToWriter(readStruct interface{}, writeTo http.ResponseWriter) {
	writeTo.Header().Set("Content-Type", "application/json")
	err := json.NewEncoder(writeTo).Encode(readStruct)
	CheckErr(err)
}

func CheckErr(err error) {
	if err != nil {
		_, fn, line, _ := runtime.Caller(1)
		logrus.WithFields(logrus.Fields{"file": fn, "line": line}).Error(err.Error())
	}
}
