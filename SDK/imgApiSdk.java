

import org.springframework.web.multipart.MultipartFile;
import sun.misc.BASE64Encoder;

import java.io.*;
import java.net.URL;
import java.net.URLConnection;

/**
 * 超简图床JAVA版本SDK
 */
public class imgApiSdk {

    /**
     * 上传图片到超简图床
     * @param apiUrl api地址，请在图床设置页面查看
     * @param apiKey 通讯秘钥，请在图床设置页面查看
     * @param file   表单的图片文件
     * @return
     */
    public static String uploadToImgApi(String apiUrl, String apiKey, MultipartFile file){
        String base64 = null;
        File f = null;
        String res = null;
        try {
            f=File.createTempFile("tmp", null);
            file.transferTo(f);
            f.deleteOnExit();     //使用完成删除文件

            FileInputStream inputFile = new FileInputStream(f);

            byte[] buffer = new byte[(int) f.length()];
            inputFile.read(buffer);
            inputFile.close();
            base64 = new BASE64Encoder().encode(buffer);
            base64 = base64.replaceAll("[\\s*\t\n\r]", "");

            String param = "key="+apiKey+"&onlyUrl=1&imgBase64="+getURLEncoderString(base64);


            res = sendPost(apiUrl,param);



        } catch (IOException e) {
            e.printStackTrace();
        }

        return res;
    }

    /**
     * 向指定 URL 发送POST方法的请求
     *
     * @param url
     *            发送请求的 URL
     * @param param
     *            请求参数，请求参数应该是 name1=value1&name2=value2 的形式。
     * @return 所代表远程资源的响应结果
     */
    private static String sendPost(String url, String param) {
        PrintWriter out = null;
        BufferedReader in = null;
        String result = "";
        try {
            URL realUrl = new URL(url);
            // 打开和URL之间的连接
            URLConnection conn = realUrl.openConnection();
            // 设置通用的请求属性
            conn.setRequestProperty("accept", "*/*");
            conn.setRequestProperty("connection", "Keep-Alive");
            conn.setRequestProperty("user-agent",
                    "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1;SV1)");
            // 发送POST请求必须设置如下两行
            conn.setDoOutput(true);
            conn.setDoInput(true);
            // 获取URLConnection对象对应的输出流
            out = new PrintWriter(conn.getOutputStream());
            // 发送请求参数
            out.print(param);
            // flush输出流的缓冲
            out.flush();
            // 定义BufferedReader输入流来读取URL的响应
            in = new BufferedReader(
                    new InputStreamReader(conn.getInputStream()));
            String line;
            while ((line = in.readLine()) != null) {
                result += line;
            }
        } catch (Exception e) {
            System.out.println("发送 POST 请求出现异常！"+e);
            e.printStackTrace();
        }
        //使用finally块来关闭输出流、输入流
        finally{
            try{
                if(out!=null){
                    out.close();
                }
                if(in!=null){
                    in.close();
                }
            }
            catch(IOException ex){
                ex.printStackTrace();
            }
        }
        return result;
    }

    /**
     * URL编码
     * @param str 需要编码的文本
     * @return 编码后的文本
     */
    private static String getURLEncoderString(String str) {
        String result = "";
        if (null == str) {
            return "";
        }
        try {
            result = java.net.URLEncoder.encode(str, "UTF-8");
        } catch (UnsupportedEncodingException e) {
            e.printStackTrace();
        }
        return result;
    }
}
