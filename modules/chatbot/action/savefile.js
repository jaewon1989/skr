
	//file save Test
	@RequestMapping(value = "/saveFile", method = RequestMethod.POST)
	public void saveFile(Locale locale,
			MultipartHttpServletRequest req,
			HttpServletResponse res,
			HttpSession session,
			@RequestParam Map<String,Object> pMap) throws Exception {
	    
		System.out.println("ddd");
//		Map<String, Object> reqData = initCtrl(req, session, pMap);
		JsonObject result = new JsonObject();
		
//		String uuid = String.valueOf(reqData.get(NetKey.UUID));

		MultipartHttpServletRequest multiReq = (MultipartHttpServletRequest) req;
		Map<String, MultipartFile> fileMap = multiReq.getFileMap();
		
			
		for(Map.Entry<String, MultipartFile> file : fileMap.entrySet()){

			System.out.println("zz");
			MultipartFile f = file.getValue(); 
			File rootDir = getFileSaveTest();
            if(file.getValue().getSize() !=0 && !file.getValue().isEmpty()){
	            String dir = "testFileSave/";
	            
	            long currentTime = System.currentTimeMillis();
	            SimpleDateFormat simDf = new SimpleDateFormat("yyyyMMddHHmmss");
	           
	            String rootPath = rootDir.getAbsolutePath() + File.separator;
	            String uploadFile = f.getOriginalFilename();
	            System.out.println(uploadFile);
	            String extension = uploadFile.substring(uploadFile.lastIndexOf(".")+1); // jpg
				String newFileName = "_" + simDf.format(new Date(currentTime)) +"."+ extension;
				String thumbnailFileName = "_thumbnail_" + simDf.format(new Date(currentTime)) +"."+ extension;
	
				String newFileFullPath = rootPath + uploadFile;
				String newThumbFullPath = rootPath + thumbnailFileName;
				
//				if(new File(newFileFullPath).exists()){
//					newFileName = uploadFile;
//					newFileFullPath = rootPath + newFileName;
//				}
								
				
				f.transferTo(new File(newFileFullPath));
				
			}
			
		}
			
		ResCode code = ResCode.RES_SUCC;
		outResult(locale, req, res, result, code);
				
	}

   protected File getFileSaveTest(){
      File dir = new File("/Maemi/testFileSave/");
        
        if(!dir.isDirectory()){
           dir.mkdirs();
        }
        
        return dir;
   }