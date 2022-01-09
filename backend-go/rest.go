package main

import (
	"encoding/base64"
	"encoding/json"
	"fmt"
	"github.com/gorilla/handlers"
	"github.com/gorilla/mux"
	"github.com/xyrk/covid2019/ihandlers"
	"github.com/spf13/viper"
	"log"
	"net/http"
	"runtime"
	"strings"
)

func CheckErr(err error) {
	if err != nil {
		_, fn, line, _ := runtime.Caller(1)
		msg := fmt.Sprintf("[error] %s:%d %v", fn, line, err.Error())
		log.Println(msg)
	}
}

func PrintStructToWriter(readStruct interface{}, writeTo http.ResponseWriter) {
	writeTo.Header().Set("Content-Type", "application/json")
	err := json.NewEncoder(writeTo).Encode(readStruct)
	CheckErr(err)
}

func PrintErrorToWriter(w http.ResponseWriter, err error) {
	retError := map[string]string{"error": err.Error()}
	PrintStructToWriter(retError, w)
	CheckErr(err)
}

func GetReplaceMe(w http.ResponseWriter, req *http.Request) {
	params := mux.Vars(req)
	param := params["param"]

	result := map[string]string{"data": param}
	PrintStructToWriter(result, w)
}

// use provides a cleaner interface for chaining middleware for single routes.
// Middleware functions are simple HTTP ihandlers (w http.ResponseWriter, r *http.Request)
//
//  r.HandleFunc("/login", use(loginHandler, rateLimit, csrf))
//  r.HandleFunc("/form", use(formHandler, csrf))
//  r.HandleFunc("/about", aboutHandler)
//
// See https://gist.github.com/elithrar/7600878#comment-955958 for how to extend it to suit simple http.Handler's
func use(h http.HandlerFunc, middleware ...func(http.HandlerFunc) http.HandlerFunc) http.HandlerFunc {
	for _, m := range middleware {
		h = m(h)
	}

	return h
}

// Leverages nemo's answer in http://stackoverflow.com/a/21937924/556573
func basicAuth(h http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {

		w.Header().Set("WWW-Authenticate", `Basic realm="Restricted"`)

		s := strings.SplitN(r.Header.Get("Authorization"), " ", 2)
		if len(s) != 2 {
			http.Error(w, "Not authorized", 401)
			return
		}

		b, err := base64.StdEncoding.DecodeString(s[1])
		if err != nil {
			http.Error(w, err.Error(), 401)
			return
		}

		pair := strings.SplitN(string(b), ":", 2)
		if len(pair) != 2 {
			http.Error(w, "Not authorized", 401)
			return
		}

		if pair[0] != viper.GetString("username") || pair[1] != viper.GetString("password") {
			http.Error(w, "Not authorized", 401)
			return
		}

		h.ServeHTTP(w, r)
	}
}

func init() {
	viper.SetConfigName("config")
	viper.SetConfigType("yaml")
	viper.AddConfigPath(".")
	err := viper.ReadInConfig()
	if err != nil {
		log.Fatal(fmt.Sprintf("Can not find config file. Err: %v", err.Error()))
	}
}

func main() {
	router := mux.NewRouter()
	//router.HandleFunc("/api/v2/contacts", use(ihandlers.GetContacts, basicAuth)).Methods("GET")
	router.HandleFunc("/api/v2/contacts/{id}", ihandlers.GetContact).Methods("GET")
	router.HandleFunc("/api/v2/contacts", ihandlers.GetContacts).Methods("GET")
	router.HandleFunc("/api/v2/contacts", ihandlers.UpdateContacts).Methods("PUT")
	router.HandleFunc("/api/v2/contacts/create", ihandlers.CreateContact).Methods("POST")
	router.HandleFunc("/api/v2/contacts/delete", ihandlers.DeleteContacts).Methods("POST")

	router.HandleFunc("/api/v2/upload-contacts", ihandlers.UploadContacts).Methods("POST")

	router.HandleFunc("/api/v2/campaigns", ihandlers.GetCampaigns).Methods("GET")
	router.HandleFunc("/api/v2/campaigns/create", ihandlers.CreateCampaign).Methods("POST")
	router.HandleFunc("/api/v2/campaigns/delete", ihandlers.DeleteCampaigns).Methods("POST")
	router.HandleFunc("/api/v2/campaign-call-records", ihandlers.CreateCampaignCallRecords).Methods("POST")

	//router.HandleFunc("/api/v2/replaceme/{param}", use(GetReplaceMe, basicAuth)).Methods("GET")

	//prepare for cross origin request from datatables ajax
	headersOk := handlers.AllowedHeaders([]string{"content-type"})
	originsOk := handlers.AllowedOrigins([]string{"*"})
	methodsOk := handlers.AllowedMethods([]string{"GET", "HEAD", "POST", "PUT", "OPTIONS"})
	log.Fatal(http.ListenAndServe(viper.GetString("port"), handlers.CORS(headersOk, originsOk, methodsOk)(router)))
}
