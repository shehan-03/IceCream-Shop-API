namespace IceCreamShop.Desktop.Models;

public class LoginResult
{
    public string Token { get; set; } = "";
    public User? User { get; set; }
}