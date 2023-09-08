
    // javascript proxy for webservices
    // by Matthias Hertel
    /*A NedapRfidReader Access Service*/
     
     proxies.NedapRfidWebService = {
     url: url_serveur_rfid,
     ns: "http://www.nedaplibrary.com/NedapRfidWebService/"
     } // proxies.NedapRfidWebService
     
        /** Returns the version number of the webservice */
       
       proxies.NedapRfidWebService.GetWebserviceVersion 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.GetWebserviceVersion.fname
        = "GetWebserviceVersion";
       proxies.NedapRfidWebService.GetWebserviceVersion.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.GetWebserviceVersion.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/GetWebserviceVersion";
       proxies.NedapRfidWebService.GetWebserviceVersion.params
        = [];
      proxies.NedapRfidWebService.
         GetWebserviceVersion.rtype 
         = [
          "GetWebserviceVersionResult"
        ];
    
        /** Lower case 'R' variant of the ReadLabel method */
       
       proxies.NedapRfidWebService.readLabel 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.readLabel.fname
        = "readLabel";
       proxies.NedapRfidWebService.readLabel.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.readLabel.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/readLabel";
       proxies.NedapRfidWebService.readLabel.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "readData"
        ];
      proxies.NedapRfidWebService.
         readLabel.rtype 
         = [
          "RfidLabels"
        ];
    
        /** Polls for labels that are currently in the field, reads the data and determines their types. Ipaddress is the network address of the reader that needs to be polled. Timeout is the maximum timeout that the webservice can wait for new incoming labels. ReadData set to false only returns the UID of all the labels present, ReadData set to true returns the interpreted data contents */
       
       proxies.NedapRfidWebService.ReadLabel 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.ReadLabel.fname
        = "ReadLabel";
       proxies.NedapRfidWebService.ReadLabel.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.ReadLabel.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/ReadLabel";
       proxies.NedapRfidWebService.ReadLabel.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "readData"
        ];
      proxies.NedapRfidWebService.
         ReadLabel.rtype 
         = [
          "RfidLabels"
        ];
    
        /** Enables the EAS bit of the label with the given UID. Returns true if successfull, False if it failed */
       
       proxies.NedapRfidWebService.EnableEAS 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.EnableEAS.fname
        = "EnableEAS";
       proxies.NedapRfidWebService.EnableEAS.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.EnableEAS.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/EnableEAS";
       proxies.NedapRfidWebService.EnableEAS.params
        = [
          "ipaddress"
        ,
          "timeout:int"
        ,
          "UID"
        ];
      proxies.NedapRfidWebService.
         EnableEAS.rtype 
         = [
          "EnableEASResult"
        ];
    
        /** Disables the EAS bit of the label with the given UID. Returns true if successfull, False if it failed */
       
       proxies.NedapRfidWebService.DisableEAS 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.DisableEAS.fname
        = "DisableEAS";
       proxies.NedapRfidWebService.DisableEAS.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.DisableEAS.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/DisableEAS";
       proxies.NedapRfidWebService.DisableEAS.params
        = [
          "ipaddress"
        ,
          "timeout:int"
        ,
          "UID"
        ];
      proxies.NedapRfidWebService.
         DisableEAS.rtype 
         = [
          "DisableEASResult"
        ];
    
        /** Writes data */
       
       proxies.NedapRfidWebService.WriteNedapLabel 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.WriteNedapLabel.fname
        = "WriteNedapLabel";
       proxies.NedapRfidWebService.WriteNedapLabel.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.WriteNedapLabel.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/WriteNedapLabel";
       proxies.NedapRfidWebService.WriteNedapLabel.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "uniqueIdentifier"
        ,
          "nedapLabel"
        ];
      proxies.NedapRfidWebService.
         WriteNedapLabel.rtype 
         = [
          "WriteNedapLabelResult"
        ];
    
        /** Writes data */
       
       proxies.NedapRfidWebService.WriteNBDLabel 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.WriteNBDLabel.fname
        = "WriteNBDLabel";
       proxies.NedapRfidWebService.WriteNBDLabel.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.WriteNBDLabel.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/WriteNBDLabel";
       proxies.NedapRfidWebService.WriteNBDLabel.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "uniqueIdentifier"
        ,
          "ndbLabel"
        ];
      proxies.NedapRfidWebService.
         WriteNBDLabel.rtype 
         = [
          "WriteNBDLabelResult"
        ];
    
        /** Enables the EAS bit of the label with the given Barcode. Returns true if successfull, False if it failed */
       
       proxies.NedapRfidWebService.EnableBarcodeEAS 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.EnableBarcodeEAS.fname
        = "EnableBarcodeEAS";
       proxies.NedapRfidWebService.EnableBarcodeEAS.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.EnableBarcodeEAS.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/EnableBarcodeEAS";
       proxies.NedapRfidWebService.EnableBarcodeEAS.params
        = [
          "ipaddress"
        ,
          "timeout:int"
        ,
          "Barcode"
        ];
      proxies.NedapRfidWebService.
         EnableBarcodeEAS.rtype 
         = [
          "EnableBarcodeEASResult"
        ];
    
        /** Disables the EAS bit of the label with the given Barcode. Returns true if successfull, False if it failed */
       
       proxies.NedapRfidWebService.DisableBarcodeEAS 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.DisableBarcodeEAS.fname
        = "DisableBarcodeEAS";
       proxies.NedapRfidWebService.DisableBarcodeEAS.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.DisableBarcodeEAS.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/DisableBarcodeEAS";
       proxies.NedapRfidWebService.DisableBarcodeEAS.params
        = [
          "ipaddress"
        ,
          "timeout:int"
        ,
          "Barcode"
        ];
      proxies.NedapRfidWebService.
         DisableBarcodeEAS.rtype 
         = [
          "DisableBarcodeEASResult"
        ];
    
        /** Writes data according to the NedapLabel dataform (barcode-only in hexstring) */
       
       proxies.NedapRfidWebService.WriteNedapLabel_native 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.WriteNedapLabel_native.fname
        = "WriteNedapLabel_native";
       proxies.NedapRfidWebService.WriteNedapLabel_native.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.WriteNedapLabel_native.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/WriteNedapLabel_native";
       proxies.NedapRfidWebService.WriteNedapLabel_native.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "uniqueIdentifier"
        ,
          "barcode"
        ];
      proxies.NedapRfidWebService.
         WriteNedapLabel_native.rtype 
         = [
          "WriteNedapLabel_nativeResult"
        ];
    
        /** Writes data according to the NBD label format v4.1 */
       
       proxies.NedapRfidWebService.WriteNBDLabel_native 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.WriteNBDLabel_native.fname
        = "WriteNBDLabel_native";
       proxies.NedapRfidWebService.WriteNBDLabel_native.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.WriteNBDLabel_native.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/WriteNBDLabel_native";
       proxies.NedapRfidWebService.WriteNBDLabel_native.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "uniqueIdentifier"
        ,
          "barcode"
        ,
          "libraryIdentifier"
        ,
          "itemNr:int"
        ,
          "totalItems:int"
        ];
      proxies.NedapRfidWebService.
         WriteNBDLabel_native.rtype 
         = [
          "WriteNBDLabel_nativeResult"
        ];
    
        /** Writes data according to the FrenchLabel dataform FR01. LogisticPartGroup5 can not be written in this function */
       
       proxies.NedapRfidWebService.WriteFrenchLabelV2_native 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.WriteFrenchLabelV2_native.fname
        = "WriteFrenchLabelV2_native";
       proxies.NedapRfidWebService.WriteFrenchLabelV2_native.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.WriteFrenchLabelV2_native.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/WriteFrenchLabelV2_native";
       proxies.NedapRfidWebService.WriteFrenchLabelV2_native.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "uniqueIdentifier"
        ,
          "documentNumber"
        ,
          "libraryCode"
        ,
          "itemNr:int"
        ,
          "totalItems:int"
        ,
          "usage:int"
        ,
          "typeEas:int"
        ,
          "logisticPartGroup1"
        ,
          "logisticPartGroup2"
        ,
          "logisticPartGroup3"
        ,
          "logisticPartGroup4"
        ];
      proxies.NedapRfidWebService.
         WriteFrenchLabelV2_native.rtype 
         = [
          "WriteFrenchLabelV2_nativeResult"
        ];
    
        /** Writes data according to the FrenchLabel dataform FR01 */
       
       proxies.NedapRfidWebService.WriteFrenchLabel_native 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.WriteFrenchLabel_native.fname
        = "WriteFrenchLabel_native";
       proxies.NedapRfidWebService.WriteFrenchLabel_native.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.WriteFrenchLabel_native.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/WriteFrenchLabel_native";
       proxies.NedapRfidWebService.WriteFrenchLabel_native.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "uniqueIdentifier"
        ,
          "documentNumber"
        ,
          "libraryCode"
        ,
          "itemNr:int"
        ,
          "totalItems:int"
        ,
          "usage:int"
        ,
          "typeEas:int"
        ,
          "logisticPartGroup1"
        ,
          "logisticPartGroup2"
        ,
          "logisticPartGroup3"
        ,
          "logisticPartGroup4"
        ,
          "logisticPartGroup5"
        ];
      proxies.NedapRfidWebService.
         WriteFrenchLabel_native.rtype 
         = [
          "WriteFrenchLabel_nativeResult"
        ];
    
        /** Writes data according to the DanishLabel dataform of july 2005 */
       
       proxies.NedapRfidWebService.WriteDanishLabel_native 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.NedapRfidWebService.WriteDanishLabel_native.fname
        = "WriteDanishLabel_native";
       proxies.NedapRfidWebService.WriteDanishLabel_native.service
        = proxies.NedapRfidWebService;
       proxies.NedapRfidWebService.WriteDanishLabel_native.action
        = "http://www.nedaplibrary.com/NedapRfidWebService/WriteDanishLabel_native";
       proxies.NedapRfidWebService.WriteDanishLabel_native.params
        = [
          "ipAddress"
        ,
          "timeout:int"
        ,
          "uniqueIdentifier"
        ,
          "primaryItemId"
        ,
          "ownerLibraryCountry"
        ,
          "ownerLibrary"
        ,
          "itemNr:int"
        ,
          "totalItems:int"
        ,
          "typeOfUsage:int"
        ];
      proxies.NedapRfidWebService.
         WriteDanishLabel_native.rtype 
         = [
          "WriteDanishLabel_nativeResult"
        ];
    