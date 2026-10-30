using System.Windows;
using IceCreamShop.Desktop.Models;

namespace IceCreamShop.Desktop;

public partial class CustomerDialog : Window
{
    private readonly Customer _customer;

    public CustomerDialog(Customer? customer = null)
    {
        InitializeComponent();
        _customer = customer ?? new Customer();
        Title = customer == null ? "Add customer" : "Edit customer";

        NameBox.Text = _customer.Name;
        PhoneBox.Text = _customer.Phone;
        EmailBox.Text = _customer.Email;
        AddressBox.Text = _customer.Address;
    }

    private async void SaveButton_Click(object sender, RoutedEventArgs e)
    {
        ErrorText.Text = "";

        _customer.Name = NameBox.Text;
        _customer.Phone = PhoneBox.Text;
        _customer.Email = string.IsNullOrWhiteSpace(EmailBox.Text) ? null : EmailBox.Text;
        _customer.Address = string.IsNullOrWhiteSpace(AddressBox.Text) ? null : AddressBox.Text;

        try
        {
            if (_customer.Id == 0)
                await App.Api.CreateCustomerAsync(_customer);
            else
                await App.Api.UpdateCustomerAsync(_customer);

            DialogResult = true;
        }
        catch (Exception ex)
        {
            ErrorText.Text = ex.Message;
        }
    }
}