        '----------------------------------------------------------------------
        ' 解码函数:获取XMLHTTP返回的二进制结果并解码
        '----------------------------------------------------------------------
        Function NEWSDF__bytes2BSTR(vIn)
                Dim strReturn, i,ThisCharCode,NextCharCode
            strReturn = ""
            For i = 1 To LenB(vIn)
                ThisCharCode = AscB(MidB(vIn,i,1))
                If ThisCharCode < &H80 Then
                    strReturn = strReturn & Chr(ThisCharCode)
                Else
                    NextCharCode = AscB(MidB(vIn,i+1,1))
                    strReturn = strReturn & Chr(CLng(ThisCharCode) * &H100 + CInt(NextCharCode))
                    i = i + 1
                End If
            Next
            NEWSDF__bytes2BSTR = strReturn
        End Function

	Function NEWSDF__URLEncoding(vstrIn)
                Dim strReturn,i,innerCode,Low8,Hight8,ThisChr
		Dim unSafeChars
		unSafeChars = " ;/?:@&=+#%<>'`[],~!$^()|\" & "{{" &"}}" & """" & vbCr & vbLf
		strReturn = ""
		For i = 1 To Len(vstrIn)
              	ThisChr = Mid(vStrIn,i,1)
			If Abs(Asc(ThisChr)) < &HFF Then
				If InStr(1,unSafeChars,ThisChr) > 0 Then
					If Asc(ThisChr) < &HF Then
						strReturn = strReturn & "%0" & Hex(Asc(ThisChr))
					Else
						strReturn = strReturn & "%" & Hex(Asc(ThisChr))
					End If
				Else
					strReturn = strReturn & ThisChr
				End If
			Else
                  	innerCode = Asc(ThisChr)
				
				If innerCode < 0 Then
                      	innerCode = innerCode + &H10000
				End If
				
				Hight8 = (innerCode  And &HFF00)\ &HFF
				Low8 = innerCode And &HFF
				strReturn = strReturn & "%" & Hex(Hight8) &  "%" & Hex(Low8)
			End If
		Next
		NEWSDF__URLEncoding = strReturn
	End Function

        Function NEWSDF__getRepeatNews(vstrin)
                Dim strRequest,strResult
                
                '------------------------------------------------------------------------
                'CGI请求参数
                '       content: 正文内容
                '------------------------------------------------------------------------
                strRequest = "content=" & NEWSDF__URLEncoding(vstrin)
                
                '创建请求对象
                Set oReq = CreateObject("MSXML2.XMLHTTP")
                
                '向指定的CGI发送请求
                oReq.open "POST","getRepeatNews.cgi",false
                oReq.setRequestHeader "Content-Length",Len(strRequest)
                oReq.setRequestHeader "CONTENT-TYPE","application/x-www-form-urlencoded"
                oReq.send strRequest
                
                '分析并获取返回结果
                NEWSDF__getRepeatNews = NEWSDF__bytes2BSTR(oReq.responseBody)
        End Function
