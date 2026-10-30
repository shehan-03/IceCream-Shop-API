using System.Windows;

namespace IceCreamShop.Desktop;

public partial class LoginWindow : Window
{
    public LoginWindow()
    {
        InitializeComponent();
    }

    private async void LoginButton_Click(object sender, RoutedEventArgs e)
    {
        ErrorText.Text = "";
        LoginButton.IsEnabled = false;

        try
        {
            await App.Api.LoginAsync(EmailBox.Text, PasswordInput.Password);
            new MainWindow().Show();
            Close();
        }
        catch (Exception ex)
        {
            ErrorText.Text = ex.Message;
        }
        finally
        {
            LoginButton.IsEnabled = true;
        }
    }
}